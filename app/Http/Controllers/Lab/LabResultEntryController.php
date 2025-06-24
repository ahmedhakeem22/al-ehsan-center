<?php

namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;
use App\Models\LabTestRequest;
use App\Models\RequestedLabTestItem;
use App\Models\Patient; // For patient context in URLs
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LabResultEntryController extends Controller
{
    public function index(Request $request) // List of lab requests needing results
    {
        // $this->authorize('viewAnyResults', LabTestRequest::class); // Policy for lab technician
        $query = LabTestRequest::with(['patient:id,full_name,file_number', 'doctor:id,name'])
                               ->whereIn('status', ['sample_collected', 'processing']) // Show requests ready for results
                               ->latest('request_date');

        if ($request->filled('patient_name')) {
            $query->whereHas('patient', function ($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->patient_name . '%')
                  ->orWhere('file_number', 'like', '%' . $request->patient_name . '%');
            });
        }
        if ($request->filled('doctor_name')) {
             $query->whereHas('doctor', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->doctor_name . '%');
            });
        }
        if ($request->filled('request_date_from')) {
            $query->whereDate('request_date', '>=', $request->request_date_from);
        }
        if ($request->filled('request_date_to')) {
            $query->whereDate('request_date', '<=', $request->request_date_to);
        }

        $labRequests = $query->paginate(15)->withQueryString();

        return view('lab.results.index', compact('labRequests'));
    }

    public function showEntryForm(LabTestRequest $labRequest) // Renamed for clarity
    {
        // $this->authorize('enterResults', $labRequest);
        if (!in_array($labRequest->status, ['sample_collected', 'processing'])) {
            return redirect()->route('lab.results.index')
                            ->with('warning', 'Results can only be entered for requests with "Sample Collected" or "Processing" status.');
        }
        $labRequest->load(['patient', 'doctor', 'items.availableLabTest']);
        return view('lab.results.entry_form', compact('labRequest'));
    }

    public function saveResults(Request $request, LabTestRequest $labRequest)
    {
        // $this->authorize('enterResults', $labRequest);
        if (!in_array($labRequest->status, ['sample_collected', 'processing'])) {
            return redirect()->route('lab.results.index')
                            ->with('error', 'Cannot save results for this request state.');
        }

        $request->validate([
            'results' => 'required|array',
            'results.*.result_value' => 'nullable|string|max:255', // Per item result
            'results.*.result_unit' => 'nullable|string|max:50',
            'results.*.is_abnormal' => 'nullable|boolean',
            'results.*.notes' => 'nullable|string', // Per item notes
            'notes_from_lab' => 'nullable|string', // Overall notes from lab for this request
            'result_date' => 'required|date_format:Y-m-d H:i:s', // Or just date
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
                // Ensure $itemId is for an item belonging to this $labRequest
                $item = RequestedLabTestItem::where('id', $itemId)
                                            ->where('lab_test_request_id', $labRequest->id)
                                            ->first();
                if ($item) {
                    $item->update([
                        'result_value' => $resultData['result_value'] ?? null,
                        'result_unit' => $resultData['result_unit'] ?? null,
                        'is_abnormal' => isset($resultData['is_abnormal']) ? filter_var($resultData['is_abnormal'], FILTER_VALIDATE_BOOLEAN) : null,
                        'notes' => $resultData['notes'] ?? null,
                    ]);
                }
            }
            DB::commit();
            // Notify the requesting doctor
            // $labRequest->doctor->notify(new LabResultsAvailable($labRequest));

            return redirect()->route('clinical.lab_requests.show', [$labRequest->patient_id, $labRequest->id])
                             ->with('success', 'Lab test results saved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error saving lab test results: ' . $e->getMessage())->withInput();
        }
    }
}