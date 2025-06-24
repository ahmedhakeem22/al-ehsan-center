<?php

namespace App\Http\Controllers\Assessment;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\AssessmentItem;
use App\Models\FunctionalAssessmentForm;
use App\Models\FunctionalAssessmentResponse;
use Illuminate\Http\Request; // أو استخدم FormRequest مخصص
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Services\HijriDateService; // تأكد أن هذه الخدمة موجودة وتعمل بشكل صحيح

// إذا كنت ستستخدم Form Requests (موصى به)
// use App\Http\Requests\Assessment\StoreFunctionalAssessmentRequest;
// use App\Http\Requests\Assessment\UpdateFunctionalAssessmentRequest;

class FunctionalAssessmentController extends Controller
{
  protected HijriDateService $hijriDateService;

  public function __construct(HijriDateService $hijriDateService)
  {
    $this->hijriDateService = $hijriDateService;
  }

  public function index(Patient $patient)
  {
    $assessments = $patient->assessments()
      ->with('assessor:id,name')
      ->latest('assessment_date_gregorian')
      ->paginate(10);
    return view('assessment.functional.index', compact('patient', 'assessments'));
  }

  public function create(Patient $patient)
  {
    $assessmentItems = AssessmentItem::where('is_active', true)
      ->orderBy('axis_type')
      ->orderBy('id') // استخدم ترتيبًا ثابتًا، مثل الترتيب حسب الإنشاء أو حقل مخصص للترتيب
      ->get()
      ->groupBy('axis_type');

    if ($assessmentItems->sum(fn($group) => $group->count()) === 0) {
      return redirect()->route('assessment.functional.index', $patient->id)
        ->with('error', 'لا توجد بنود تقييم معرفة في النظام. يرجى الاتصال بالمسؤول.');
    }

    // إعداد التواريخ الافتراضية للنموذج
    $currentGregorianDate = Carbon::now()->format('Y-m-d');
    $currentHijriDate = ''; // سيتم تعيينه في الواجهة أو تركه فارغًا ليتم حسابه
    try {
      $currentHijriDate = $this->hijriDateService->getCurrentHijriDate();
    } catch (\Exception $e) {
      Log::error("Error getting current Hijri date in create: " . $e->getMessage());
      // يمكنك التعامل مع الخطأ هنا إذا لزم الأمر، مثل عرض رسالة للمستخدم
    }

    return view('assessment.functional.create', compact('patient', 'assessmentItems', 'currentGregorianDate', 'currentHijriDate'));
  }

  protected function calculateAssessmentScores(array $responses, $assessmentItemsCollection)
  {
    if ($assessmentItemsCollection->isEmpty()) {
      return [ // إرجاع قيم افتراضية أو null إذا لم تكن هناك بنود
        'medicationAxisAverage' => null,
        'psychologicalAxisAverage' => null,
        'activitiesAxisAverage' => null,
        'overallImprovementPercentage' => 0,
      ];
    }

    $totalPossibleScore = $assessmentItemsCollection->count() * 5;
    $achievedScore = 0;
    $axisScores = ['medication' => [], 'psychological' => [], 'activities' => []];

    foreach ($responses as $itemId => $rating) {
      $item = $assessmentItemsCollection->firstWhere('id', $itemId);
      if ($item) {
        $achievedScore += (int) $rating;
        if (array_key_exists($item->axis_type, $axisScores)) {
          $axisScores[$item->axis_type][] = (int) $rating;
        }
      }
    }

    $medAvg = !empty($axisScores['medication']) ? round(array_sum($axisScores['medication']) / count($axisScores['medication']), 2) : null;
    $psyAvg = !empty($axisScores['psychological']) ? round(array_sum($axisScores['psychological']) / count($axisScores['psychological']), 2) : null;
    $actAvg = !empty($axisScores['activities']) ? round(array_sum($axisScores['activities']) / count($axisScores['activities']), 2) : null;
    $overallPercentage = ($totalPossibleScore > 0) ? round(($achievedScore / $totalPossibleScore) * 100, 2) : 0;

    return [
      'medicationAxisAverage' => $medAvg,
      'psychologicalAxisAverage' => $psyAvg,
      'activitiesAxisAverage' => $actAvg,
      'overallImprovementPercentage' => $overallPercentage,
    ];
  }

  // استبدل Request بـ StoreFunctionalAssessmentRequest إذا أنشأت Form Request
  public function store(Request $request, Patient $patient)
  {
    Log::info('Assessment Store Request Data (Before Validation):', $request->all());

    $validatedData = $request->validate([
      'assessment_date_gregorian' => 'required|date_format:d/m/Y', // <--- تأكد أن هذا هو التنسيق المتوقع من النموذج
      'assessment_date_hijri_manual' => 'nullable|string|max:20',
      'recommended_stay_duration' => 'nullable|string|max:255',
      'responses' => 'required|array',
      'responses.*' => 'required|integer|min:1|max:5',
      'notes' => 'nullable|string',
    ], [
      // رسائل تحقق مخصصة إذا أردت
      'assessment_date_gregorian.date_format' => 'تنسيق تاريخ التقييم الميلادي يجب أن يكون يوم/شهر/سنة (مثال: 25/12/2024).',
    ]);

    $assessmentItems = AssessmentItem::where('is_active', true)->get();
    if ($assessmentItems->isEmpty()) {
      return back()->with('error', 'لا توجد بنود تقييم نشطة لحساب النتائج.')->withInput();
    }

    $scores = $this->calculateAssessmentScores($validatedData['responses'], $assessmentItems);

    DB::beginTransaction();
    try {
      //  تحليل التاريخ الميلادي باستخدام التنسيق المحدد الذي تم التحقق منه
      $gregorianDate = Carbon::createFromFormat('d/m/Y', $validatedData['assessment_date_gregorian']);

      // استخدم التاريخ الهجري اليدوي إذا تم توفيره، وإلا قم بالتحويل
      $hijriDate = $validatedData['assessment_date_hijri_manual'] ?: $this->hijriDateService->gregorianToHijri(
        $gregorianDate->year,
        $gregorianDate->month,
        $gregorianDate->day
      );

      $form = FunctionalAssessmentForm::create([
        'patient_id' => $patient->id,
        'assessor_id' => auth()->id(),
        'assessment_date_gregorian' => $gregorianDate->format('Y-m-d'), // <--- حفظ بالتنسيق القياسي Y-m-d
        'assessment_date_hijri' => $hijriDate,
        'recommended_stay_duration' => $validatedData['recommended_stay_duration'],
        'medication_axis_average' => $scores['medicationAxisAverage'],
        'psychological_axis_average' => $scores['psychologicalAxisAverage'],
        'activities_axis_average' => $scores['activitiesAxisAverage'],
        'overall_improvement_percentage' => $scores['overallImprovementPercentage'],
        'notes' => $validatedData['notes'],
      ]);
      $responsesData = [];
      foreach ($validatedData['responses'] as $itemId => $rating) {
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
      if (!empty($responsesData)) {
        FunctionalAssessmentResponse::insert($responsesData);
      }

      DB::commit();
      return redirect()->route('assessment.functional.show', [$patient->id, $form->id])
        ->with('success', 'تم حفظ تقييم التحسن الوظيفي بنجاح.');
    } catch (\Exception $e) {
      DB::rollBack();
      // تسجيل الخطأ بالتفصيل
      Log::error('Error saving assessment for patient ' . $patient->id . ': ' . $e->getMessage(), [
        'exception' => $e,
        'request_data' => $request->all() // تسجيل بيانات الطلب التي سببت المشكلة
      ]);
      return back()->with('error', 'حدث خطأ أثناء حفظ التقييم. التفاصيل: ' . $e->getMessage())->withInput();
    }
  }

  public function show(Patient $patient, FunctionalAssessmentForm $assessment)
  {
    if ($assessment->patient_id !== $patient->id) {
      abort(403, 'هذا التقييم لا يخص المريض المحدد.');
    }

    $assessment->load([
      'assessor:id,name',
      'responses.item' => function ($query) {
        $query->orderBy('axis_type')->orderBy('id'); // ترتيب البنود داخل الردود
      }
    ]);

    // تأكد من أن item.axis_type موجود قبل التجميع
    $responsesGroupedByAxis = $assessment->responses->filter(function ($response) {
      return isset($response->item) && isset($response->item->axis_type);
    })->groupBy('item.axis_type');


    $assessmentHistory = $patient->assessments()
      ->orderBy('assessment_date_gregorian', 'asc')
      ->get(['id', 'assessment_date_gregorian', 'overall_improvement_percentage']);

    return view('assessment.functional.show', compact('patient', 'assessment', 'responsesGroupedByAxis', 'assessmentHistory'));
  }

  public function edit(Patient $patient, FunctionalAssessmentForm $assessment)
  {
    if ($assessment->patient_id !== $patient->id) {
      abort(403, 'هذا التقييم لا يخص المريض المحدد.');
    }

    $assessmentItems = AssessmentItem::where('is_active', true)
      ->orderBy('axis_type')
      ->orderBy('id')
      ->get()
      ->groupBy('axis_type');

    if ($assessmentItems->sum(fn($group) => $group->count()) === 0) {
      return redirect()->route('assessment.functional.show', [$patient->id, $assessment->id])
        ->with('error', 'لا توجد بنود تقييم معرفة في النظام لتعديل هذا التقييم. يرجى الاتصال بالمسؤول.');
    }

    $currentResponses = $assessment->responses()->pluck('rating', 'assessment_item_id')->all();

    // لا نحتاج لتمرير currentGregorianDate و currentHijriDate هنا لأن النموذج سيأخذها من $assessment
    return view('assessment.functional.edit', compact('patient', 'assessment', 'assessmentItems', 'currentResponses'));
  }

  // استبدل Request بـ UpdateFunctionalAssessmentRequest إذا أنشأت Form Request
  public function update(Request $request, Patient $patient, FunctionalAssessmentForm $assessment)
  {
    if ($assessment->patient_id !== $patient->id) {
      abort(403, 'هذا التقييم لا يخص المريض المحدد.');
    }

    Log::info('Assessment Update Request Data:', $request->all());

    $validatedData = $request->validate([
      'assessment_date_gregorian' => 'required|date_format:Y-m-d',
      'assessment_date_hijri_manual' => 'nullable|string|max:20',
      'recommended_stay_duration' => 'nullable|string|max:255',
      'responses' => 'required|array',
      'responses.*' => 'required|integer|min:1|max:5',
      'notes' => 'nullable|string',
    ]);

    $assessmentItems = AssessmentItem::where('is_active', true)->get();
    if ($assessmentItems->isEmpty()) {
      return back()->with('error', 'لا توجد بنود تقييم نشطة لحساب النتائج.')->withInput();
    }
    $scores = $this->calculateAssessmentScores($validatedData['responses'], $assessmentItems);

    DB::beginTransaction();
    try {
      $gregorianDate = Carbon::parse($validatedData['assessment_date_gregorian']);
      $hijriDate = $validatedData['assessment_date_hijri_manual'] ?: $this->hijriDateService->gregorianToHijri($gregorianDate->year, $gregorianDate->month, $gregorianDate->day);

      $assessment->update([
        'assessor_id' => auth()->id(), // تحديث المقيم إذا قام مستخدم آخر بالتعديل
        'assessment_date_gregorian' => $gregorianDate->format('Y-m-d'),
        'assessment_date_hijri' => $hijriDate,
        'recommended_stay_duration' => $validatedData['recommended_stay_duration'],
        'medication_axis_average' => $scores['medicationAxisAverage'],
        'psychological_axis_average' => $scores['psychologicalAxisAverage'],
        'activities_axis_average' => $scores['activitiesAxisAverage'],
        'overall_improvement_percentage' => $scores['overallImprovementPercentage'],
        'notes' => $validatedData['notes'],
      ]);

      $assessment->responses()->delete(); // حذف الردود القديمة
      $responsesData = [];
      foreach ($validatedData['responses'] as $itemId => $rating) {
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
      if (!empty($responsesData)) {
        FunctionalAssessmentResponse::insert($responsesData);
      }

      DB::commit();
      return redirect()->route('assessment.functional.show', [$patient->id, $assessment->id])
        ->with('success', 'تم تحديث تقييم التحسن الوظيفي بنجاح.');
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('Error updating assessment ' . $assessment->id . ' for patient ' . $patient->id . ': ' . $e->getMessage(), ['exception' => $e]);
      return back()->with('error', 'حدث خطأ أثناء تحديث التقييم: ' . $e->getMessage())->withInput();
    }
  }

  public function destroy(Patient $patient, FunctionalAssessmentForm $assessment)
  {
    if ($assessment->patient_id !== $patient->id) {
      abort(403, 'هذا التقييم لا يخص المريض المحدد.');
    }

    DB::beginTransaction();
    try {
      // إذا كان لديك ON DELETE CASCADE في المايجريشن لجدول functional_assessment_responses
      // عند حذف form_id، فلن تحتاج لـ $assessment->responses()->delete();
      $assessment->delete();
      DB::commit();
      return redirect()->route('assessment.functional.index', $patient->id)
        ->with('success', 'تم حذف تقييم التحسن الوظيفي بنجاح.');
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('Error deleting assessment ' . $assessment->id . ' for patient ' . $patient->id . ': ' . $e->getMessage(), ['exception' => $e]);
      return redirect()->route('assessment.functional.index', $patient->id)
        ->with('error', 'حدث خطأ أثناء حذف التقييم.');
    }
  }
}