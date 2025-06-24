<?php

namespace App\Http\Controllers\Assessment;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\AssessmentItem;
use App\Models\FunctionalAssessmentForm;
use App\Models\FunctionalAssessmentResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\HijriDateService; // خدمة لتحويل التاريخ (تحتاج لإنشائها أو استخدام مكتبة)

class FunctionalAssessmentController extends Controller
{
    protected HijriDateService $hijriDateService;

    public function __construct(HijriDateService $hijriDateService)
    {
        $this->hijriDateService = $hijriDateService;
    }

    public function index(Patient $patient)
    {
        $assessments = $patient->assessments()->with('assessor')->latest('assessment_date_gregorian')->paginate(10);
        return view('assessment.functional.index', compact('patient', 'assessments'));
    }

    public function create(Patient $patient)
    {
        $assessmentItems = AssessmentItem::where('is_active', true)
            ->orderBy('axis_type')
            ->orderBy('id') // Or some other consistent order
            ->get()
            ->groupBy('axis_type');

        if ($assessmentItems->isEmpty()) {
            return redirect()->route('assessment.functional.index', $patient->id)
                             ->with('error', 'No assessment items are defined in the system. Please contact an administrator.');
        }

        return view('assessment.functional.create', compact('patient', 'assessmentItems'));
    }

    public function store(Request $request, Patient $patient)
    {
        $request->validate([
            'assessment_date_gregorian' => 'required|date',
            'recommended_stay_duration' => 'nullable|string|max:255',
            'responses' => 'required|array',
            'responses.*' => 'required|integer|min:1|max:5', // For each item_id => rating
            'notes' => 'nullable|string',
        ]);

        $assessmentItems = AssessmentItem::where('is_active', true)->get();
        $totalPossibleScore = $assessmentItems->count() * 5;
        $achievedScore = 0;
        $axisScores = ['medication' => [], 'psychological' => [], 'activities' => []];

        foreach ($request->responses as $itemId => $rating) {
            $item = $assessmentItems->firstWhere('id', $itemId);
            if ($item) {
                $achievedScore += $rating;
                if (isset($axisScores[$item->axis_type])) {
                    $axisScores[$item->axis_type][] = $rating;
                }
            }
        }

        $medicationAxisAverage = !empty($axisScores['medication']) ? array_sum($axisScores['medication']) / count($axisScores['medication']) : null;
        $psychologicalAxisAverage = !empty($axisScores['psychological']) ? array_sum($axisScores['psychological']) / count($axisScores['psychological']) : null;
        $activitiesAxisAverage = !empty($axisScores['activities']) ? array_sum($axisScores['activities']) / count($axisScores['activities']) : null;
        $overallImprovementPercentage = ($totalPossibleScore > 0) ? ($achievedScore / $totalPossibleScore) * 100 : 0;

        DB::beginTransaction();
        try {
            $gregorianDate = Carbon::parse($request->assessment_date_gregorian);
            $hijriDate = $this->hijriDateService->gregorianToHijri($gregorianDate->year, $gregorianDate->month, $gregorianDate->day);


            $form = FunctionalAssessmentForm::create([
                'patient_id' => $patient->id,
                'assessor_id' => auth()->id(),
                'assessment_date_gregorian' => $gregorianDate,
                'assessment_date_hijri' => $hijriDate, // YYYY-MM-DD format
                'recommended_stay_duration' => $request->recommended_stay_duration,
                'medication_axis_average' => $medicationAxisAverage,
                'psychological_axis_average' => $psychologicalAxisAverage,
                'activities_axis_average' => $activitiesAxisAverage,
                'overall_improvement_percentage' => round($overallImprovementPercentage, 2),
                'notes' => $request->notes,
            ]);

            $responsesData = [];
            foreach ($request->responses as $itemId => $rating) {
                 if ($assessmentItems->contains('id', $itemId)) {
                    $responsesData[] = [
                        'assessment_form_id' => $form->id,
                        'assessment_item_id' => $itemId,
                        'rating' => $rating,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
            FunctionalAssessmentResponse::insert($responsesData);

            DB::commit();
            return redirect()->route('assessment.functional.show', [$patient->id, $form->id])
                             ->with('success', 'Functional assessment saved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error saving assessment: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Patient $patient, FunctionalAssessmentForm $assessment)
    {
        // Eager load related data for display
        $assessment->load(['assessor', 'responses.item']);
        $responsesGrouped = $assessment->responses->groupBy('item.axis_type');

        // For trend chart (simplified example)
        $assessmentHistory = $patient->assessments()
                                     ->orderBy('assessment_date_gregorian', 'asc')
                                     ->get(['assessment_date_gregorian', 'overall_improvement_percentage']);

        return view('assessment.functional.show', compact('patient', 'assessment', 'responsesGrouped', 'assessmentHistory'));
    }

    public function edit(Patient $patient, FunctionalAssessmentForm $assessment)
    {
        $assessmentItems = AssessmentItem::where('is_active', true)
            ->orderBy('axis_type')
            ->orderBy('id')
            ->get()
            ->groupBy('axis_type');

        $currentResponses = $assessment->responses()->pluck('rating', 'assessment_item_id')->toArray();

        return view('assessment.functional.edit', compact('patient', 'assessment', 'assessmentItems', 'currentResponses'));
    }

    public function update(Request $request, Patient $patient, FunctionalAssessmentForm $assessment)
    {
        $request->validate([
            'assessment_date_gregorian' => 'required|date',
            'recommended_stay_duration' => 'nullable|string|max:255',
            'responses' => 'required|array',
            'responses.*' => 'required|integer|min:1|max:5',
            'notes' => 'nullable|string',
        ]);

        // Recalculate scores
        $assessmentItems = AssessmentItem::where('is_active', true)->get();
        $totalPossibleScore = $assessmentItems->count() * 5;
        $achievedScore = 0;
        $axisScores = ['medication' => [], 'psychological' => [], 'activities' => []];

        foreach ($request->responses as $itemId => $rating) {
            $item = $assessmentItems->firstWhere('id', $itemId);
            if ($item) {
                $achievedScore += $rating;
                 if (isset($axisScores[$item->axis_type])) {
                    $axisScores[$item->axis_type][] = $rating;
                }
            }
        }

        $medicationAxisAverage = !empty($axisScores['medication']) ? array_sum($axisScores['medication']) / count($axisScores['medication']) : null;
        $psychologicalAxisAverage = !empty($axisScores['psychological']) ? array_sum($axisScores['psychological']) / count($axisScores['psychological']) : null;
        $activitiesAxisAverage = !empty($axisScores['activities']) ? array_sum($axisScores['activities']) / count($axisScores['activities']) : null;
        $overallImprovementPercentage = ($totalPossibleScore > 0) ? ($achievedScore / $totalPossibleScore) * 100 : 0;

        DB::beginTransaction();
        try {
            $gregorianDate = Carbon::parse($request->assessment_date_gregorian);
            $hijriDate = $this->hijriDateService->gregorianToHijri($gregorianDate->year, $gregorianDate->month, $gregorianDate->day);

            $assessment->update([
                'assessor_id' => auth()->id(), // Update assessor if a different user edits
                'assessment_date_gregorian' => $gregorianDate,
                'assessment_date_hijri' => $hijriDate,
                'recommended_stay_duration' => $request->recommended_stay_duration,
                'medication_axis_average' => $medicationAxisAverage,
                'psychological_axis_average' => $psychologicalAxisAverage,
                'activities_axis_average' => $activitiesAxisAverage,
                'overall_improvement_percentage' => round($overallImprovementPercentage, 2),
                'notes' => $request->notes,
            ]);

            // Delete old responses and insert new ones
            $assessment->responses()->delete();
            $responsesData = [];
            foreach ($request->responses as $itemId => $rating) {
                 if ($assessmentItems->contains('id', $itemId)) {
                    $responsesData[] = [
                        'assessment_form_id' => $assessment->id,
                        'assessment_item_id' => $itemId,
                        'rating' => $rating,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
            FunctionalAssessmentResponse::insert($responsesData);

            DB::commit();
            return redirect()->route('assessment.functional.show', [$patient->id, $assessment->id])
                             ->with('success', 'Functional assessment updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating assessment: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Patient $patient, FunctionalAssessmentForm $assessment)
    {
        DB::beginTransaction();
        try {
            $assessment->responses()->delete();
            $assessment->delete();
            DB::commit();
            return redirect()->route('assessment.functional.index', $patient->id)
                             ->with('success', 'Functional assessment deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('assessment.functional.index', $patient->id)
                             ->with('error', 'Error deleting assessment: ' . $e->getMessage());
        }
    }
}