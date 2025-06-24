@csrf
<div class="row">
    <div class="col-md-6">
        <div class="form-group local-forms">
            <label for="assessment_date_gregorian">تاريخ التقييم (ميلادي) <span class="text-danger">*</span></label>
            <input type="text" class="form-control datetimepicker @error('assessment_date_gregorian') is-invalid @enderror"
                   id="assessment_date_gregorian" name="assessment_date_gregorian"
                   value="{{ old('assessment_date_gregorian', isset($assessment) ? $assessment->assessment_date_gregorian->format('Y-m-d') : ($currentGregorianDate ?? \Carbon\Carbon::now()->format('Y-m-d'))) }}"
                   required placeholder="YYYY-MM-DD">
            @error('assessment_date_gregorian')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group local-forms">
            <label for="assessment_date_hijri_manual">تاريخ التقييم (هجري - اختياري)</label>
            <input type="text" class="form-control @error('assessment_date_hijri_manual') is-invalid @enderror"
                   id="assessment_date_hijri_manual" name="assessment_date_hijri_manual"
                   value="{{ old('assessment_date_hijri_manual', $assessment->assessment_date_hijri ?? ($currentHijriDate ?? '')) }}"
                   placeholder="مثال: 1445-12-08">
            <small class="form-text text-muted">سيتم التحويل من الميلادي إذا ترك فارغًا. أدخل يدويًا إذا كان هناك اختلاف أو للتوثيق.</small>
            @error('assessment_date_hijri_manual')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-12">
        <div class="form-group local-forms">
            <label for="recommended_stay_duration">مدة البقاء الموصى بها</label>
            <input type="text" class="form-control @error('recommended_stay_duration') is-invalid @enderror"
                   id="recommended_stay_duration" name="recommended_stay_duration"
                   value="{{ old('recommended_stay_duration', $assessment->recommended_stay_duration ?? '') }}">
            @error('recommended_stay_duration')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<hr class="my-4">
<h4>بنود التقييم:</h4>
@if($assessmentItems && $assessmentItems->count() > 0 && $assessmentItems->sum(fn($group) => $group->count()) > 0)
    @foreach ($assessmentItems as $axisType => $items)
        @if($items->count() > 0)
            <div class="mb-4 p-3 border rounded">
                <h5 class="mb-3">
                    @if ($axisType == 'medication')
                        <i class="fas fa-pills me-2 text-primary"></i>المحور الأول: التحسن المرتبط بالأدوية النفسية
                    @elseif ($axisType == 'psychological')
                        <i class="fas fa-brain me-2 text-success"></i>المحور الثاني: التحسن الناتج عن التدخلات النفسية
                    @elseif ($axisType == 'activities')
                        <i class="fas fa-running me-2 text-info"></i>المحور الثالث: التحسن الناتج عن الأنشطة الرياضية والترفيهية
                    @else
                        {{ ucfirst($axisType) }}
                    @endif
                </h5>
                @error('responses') {{-- خطأ عام لمصفوفة الردود --}}
                    <div class="alert alert-danger p-2 small">{{ $message }}</div>
                @enderror
                @foreach ($items as $itemIndex => $item)
                    <div class="form-group row mb-3 align-items-center border-bottom pb-2">
                        <div class="col-sm-8">
                            <label for="response_{{ $item->id }}" class="form-label mb-0">
                                {{ $loop->parent->iteration }}.{{ $itemIndex + 1 }}. {{ $item->item_text_ar }}
                            </label>
                            <small class="d-block text-muted">المعيار: (1: {{ $item->criteria_1_ar }} --- 5: {{ $item->criteria_5_ar }})</small>
                        </div>
                        <div class="col-sm-4">
                            <div class="btn-group w-100" role="group" aria-label="Rating for item {{ $item->id }}">
                                @for ($i = 1; $i <= 5; $i++)
                                    <input type="radio" class="btn-check" name="responses[{{ $item->id }}]" id="response_{{ $item->id }}_{{ $i }}" value="{{ $i }}"
                                        {{ old('responses.'.$item->id, isset($currentResponses[$item->id]) ? $currentResponses[$item->id] : null) == $i ? 'checked' : '' }} required>
                                    <label class="btn btn-outline-secondary" for="response_{{ $item->id }}_{{ $i }}">{{ $i }}</label>
                                @endfor
                            </div>
                             @error('responses.'.$item->id)
                                <div class="invalid-feedback d-block">{{ $message }}</div> {{-- d-block لإظهار الخطأ تحت أزرار الراديو --}}
                            @enderror
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endforeach
@else
    <div class="alert alert-warning">لا توجد بنود تقييم متاحة حاليًا. يرجى إضافتها من إعدادات النظام.</div>
@endif
<hr class="my-4">

<div class="row">
    <div class="col-md-12">
        <div class="form-group local-forms">
            <label for="notes">ملاحظات إضافية على التقييم</label>
            <textarea class="form-control @error('notes') is-invalid @enderror"
                      id="notes" name="notes" rows="4" placeholder="أدخل أي ملاحظات إضافية هنا...">{{ old('notes', $assessment->notes ?? '') }}</textarea>
            @error('notes')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="text-end mt-4">
    <button type="submit" class="btn btn-primary btn-lg submit-form me-2">
        <i class="fas fa-save me-1"></i>
        {{ isset($assessment) ? 'تحديث التقييم' : 'حفظ التقييم' }}
    </button>
    <a href="{{ route('assessment.functional.index', $patient->id) }}" class="btn btn-outline-secondary btn-lg cancel-form">
        <i class="fas fa-times me-1"></i>
        إلغاء
    </a>
</div>