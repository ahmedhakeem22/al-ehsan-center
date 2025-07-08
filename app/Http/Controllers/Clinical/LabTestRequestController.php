<?php

namespace App\Http\Controllers\Clinical;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\LabTestRequest;
use App\Models\RequestedLabTestItem;
use App\Models\AvailableLabTest;
use App\Models\User; // For doctor/lab tech list
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LabTestRequestController extends Controller
{
    public function index(Patient $patient, Request $request)
    {
        $query = $patient->labTestRequests()
                         ->with(['doctor:id,name', 'labTechnician:id,name'])
                         ->latest('request_date');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        $labRequests = $query->paginate(10)->withQueryString();
        $statuses = LabTestRequest::distinct()->pluck('status')->mapWithKeys(fn($s) => [$s => ucfirst(str_replace('_', ' ', $s))]);
        $doctors = User::whereHas('role', fn($q) => $q->where('name', 'Doctor'))->orderBy('name')->pluck('name', 'id');

        return view('clinical.lab_requests.index', compact('patient', 'labRequests', 'statuses', 'doctors'));
    }

    public function create(Patient $patient)
    {
        // $this->authorize('create', LabTestRequest::class);
        $availableTests = AvailableLabTest::orderBy('name')->get(['id', 'name', 'code']);
        $statuses = [
            'pending_sample' => 'Pending Sample Collection',
            'sample_collected' => 'Sample Collected',
            'processing' => 'Processing',
            'cancelled' => 'Cancelled'
        ]; // Doctor usually creates as pending_sample
        return view('clinical.lab_requests.create', compact('patient', 'availableTests', 'statuses'));
    }

    public function store(Request $request, Patient $patient)
    {
        // $this->authorize('create', LabTestRequest::class);
        $request->validate([
            'request_date' => 'required|date',
            'status' => ['required', Rule::in(['pending_sample', 'sample_collected', 'processing', 'cancelled'])],
            'notes_from_doctor' => 'nullable|string',
            'requested_tests' => 'required|array|min:1',
            'requested_tests.*' => 'required|exists:available_lab_tests,id',
        ]);

        DB::beginTransaction();
        try {
            $labRequest = LabTestRequest::create([
                'patient_id' => $patient->id,
                'doctor_id' => auth()->id(), // Assuming logged-in user is doctor
                'request_date' => $request->request_date,
                'status' => $request->status,
                'notes_from_doctor' => $request->notes_from_doctor,
            ]);

            foreach ($request->requested_tests as $testId) {
                RequestedLabTestItem::create([
                    'lab_test_request_id' => $labRequest->id,
                    'available_lab_test_id' => $testId,
                ]);
            }
            DB::commit();
            return redirect()->route('clinical.lab_requests.show', [$patient->id, $labRequest->id])
                             ->with('success', 'Lab test request created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating lab test request: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Patient $patient, LabTestRequest $labRequest)
    {
        if ($labRequest->patient_id !== $patient->id) abort(404);
        $labRequest->load(['doctor', 'labTechnician', 'items.availableLabTest']);
        return view('clinical.lab_requests.show', compact('patient', 'labRequest'));
    }

    public function edit(Patient $patient, LabTestRequest $labRequest) // For Doctor to edit before results
    {
        if ($labRequest->patient_id !== $patient->id) abort(404);
        // $this->authorize('update', $labRequest);
        if ($labRequest->status === 'completed' || $labRequest->status === 'processing') {
             return redirect()->route('clinical.lab_requests.show', [$patient->id, $labRequest->id])
                             ->with('error', 'Cannot edit a request that is processing or completed.');
        }

        $availableTests = AvailableLabTest::orderBy('name')->get(['id', 'name', 'code']);
        $selectedTests = $labRequest->items()->pluck('available_lab_test_id')->toArray();
        $statuses = [
            'pending_sample' => 'Pending Sample Collection',
            'sample_collected' => 'Sample Collected',
            'processing' => 'Processing', // Lab tech might update to this
            'cancelled' => 'Cancelled'
        ];
        return view('clinical.lab_requests.edit', compact('patient', 'labRequest', 'availableTests', 'selectedTests', 'statuses'));
    }

    public function update(Request $request, Patient $patient, LabTestRequest $labRequest) // For Doctor
    {
        if ($labRequest->patient_id !== $patient->id) abort(404);
        // $this->authorize('update', $labRequest);
         if ($labRequest->status === 'completed' || $labRequest->status === 'processing') {
             return redirect()->route('clinical.lab_requests.show', [$patient->id, $labRequest->id])
                             ->with('error', 'Cannot update a request that is processing or completed.');
        }

        $request->validate([
            'request_date' => 'required|date',
            'status' => ['required', Rule::in(['pending_sample', 'sample_collected', 'processing', 'cancelled'])],
            'notes_from_doctor' => 'nullable|string',
            'requested_tests' => 'required|array|min:1',
            'requested_tests.*' => 'required|exists:available_lab_tests,id',
        ]);

        DB::beginTransaction();
        try {
            $labRequest->update($request->only(['request_date', 'status', 'notes_from_doctor']));

            // Sync requested test items
            $labRequest->items()->whereNotIn('available_lab_test_id', $request->requested_tests)->delete();
            $existingTestIds = $labRequest->items()->pluck('available_lab_test_id')->toArray();
            $newTestIds = array_diff($request->requested_tests, $existingTestIds);

            foreach ($newTestIds as $testId) {
                RequestedLabTestItem::create([
                    'lab_test_request_id' => $labRequest->id,
                    'available_lab_test_id' => $testId,
                ]);
            }

            DB::commit();
            return redirect()->route('clinical.lab_requests.show', [$patient->id, $labRequest->id])
                             ->with('success', 'Lab test request updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating lab test request: ' . $e->getMessage())->withInput();
        }
    }


    public function enterResultsForm(Patient $patient, LabTestRequest $labRequest) // For Lab Technician
    {
        if ($labRequest->patient_id !== $patient->id) abort(404);
        // $this->authorize('enterResults', $labRequest); // Policy
        if (!in_array($labRequest->status, ['sample_collected', 'processing'])) {
            return redirect()->route('clinical.lab_requests.show', [$patient->id, $labRequest->id])
                            ->with('error', 'Results can only be entered for requests with collected/processing status.');
        }
        $labRequest->load('items.availableLabTest');
        return view('clinical.lab_requests.enter_results', compact('patient', 'labRequest'));
    }


    public function saveResults(Request $request, Patient $patient, LabTestRequest $labRequest) // For Lab Technician
    {
        if ($labRequest->patient_id !== $patient->id) abort(404);
        // $this->authorize('enterResults', $labRequest); // Policy

        $request->validate([
            'results' => 'required|array',
            'results.*.result_value' => 'nullable|string|max:255',
            'results.*.result_unit' => 'nullable|string|max:50',
            'results.*.is_abnormal' => 'nullable|boolean',
            'results.*.notes' => 'nullable|string',
            'notes_from_lab' => 'nullable|string',
            'result_date' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $labRequest->update([
                'status' => 'completed',
                'lab_technician_id' => auth()->id(),
                'result_date' => $request->result_date,
                'notes_from_lab' => $request->notes_from_lab,
            ]);

            foreach ($request->results as $itemId => $resultData) {
                $item = RequestedLabTestItem::find($itemId);
                if ($item && $item->lab_test_request_id === $labRequest->id) {
                    $item->update([
                        'result_value' => $resultData['result_value'] ?? null,
                        'result_unit' => $resultData['result_unit'] ?? null,
                        'is_abnormal' => isset($resultData['is_abnormal']) ? filter_var($resultData['is_abnormal'], FILTER_VALIDATE_BOOLEAN) : null,
                        'notes' => $resultData['notes'] ?? null,
                    ]);
                }
            }
            DB::commit();
             // Add Notification for Doctor
            return redirect()->route('clinical.lab_requests.show', [$patient->id, $labRequest->id])
                             ->with('success', 'Lab test results saved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error saving lab test results: ' . $e->getMessage())->withInput();
        }
    }


    public function destroy(Patient $patient, LabTestRequest $labRequest)
    {
        if ($labRequest->patient_id !== $patient->id) abort(404);
        // $this->authorize('delete', $labRequest);
        if ($labRequest->status === 'completed') {
             return redirect()->route('clinical.lab_requests.index', $patient->id)
                             ->with('error', 'Cannot delete a completed lab test request.');
        }

        DB::beginTransaction();
        try {
            $labRequest->items()->delete();
            $labRequest->delete();
            DB::commit();
            return redirect()->route('clinical.lab_requests.index', $patient->id)
                             ->with('success', 'Lab test request deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('clinical.lab_requests.index', $patient->id)
                             ->with('error', 'Error deleting lab test request: ' . $e->getMessage());
        }
    }
}