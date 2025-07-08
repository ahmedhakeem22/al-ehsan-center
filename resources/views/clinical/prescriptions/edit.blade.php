@extends('layout.mainlayout')
@section('title', 'تعديل الوصفة الطبية #' . $prescription->id)

@section('content')
    <div class="page-wrapper">
        <div class="content">
            @component('components.page-header')
                @slot('title')
                    تعديل الوصفة الطبية #{{ $prescription->id }} للمريض <a href="{{ route('patient_management.patients.show', $patient->id) }}">{{ $patient->full_name }}</a>
                @endslot
                @slot('li_1')
                    الوصفات الطبية
                @endslot
            @endcomponent

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">تعديل بيانات الوصفة</h4>
                        </div>
                        <div class="card-body">
                             @if ($errors->any())
                                <div class="alert alert-danger">
                                    <p><strong>الرجاء إصلاح الأخطاء التالية:</strong></p>
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <form action="{{ route('clinical.prescriptions.update', [$patient->id, $prescription->id]) }}" method="POST">
                                @method('PUT')
                                @csrf

                                {{-- ======================================================= --}}
                                {{--           BEGIN: Main Prescription Form Fields          --}}
                                {{-- ======================================================= --}}
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="prescription_date">تاريخ الوصفة <span class="text-danger">*</span></label>
                                            <input type="datetime-local" class="form-control @error('prescription_date') is-invalid @enderror" id="prescription_date" name="prescription_date"
                                                   value="{{ old('prescription_date', \Carbon\Carbon::parse($prescription->prescription_date)->format('Y-m-d\TH:i')) }}" required>
                                            @error('prescription_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="doctor_id">الطبيب <span class="text-danger">*</span></label>
                                            <select class="form-control @error('doctor_id') is-invalid @enderror" name="doctor_id" id="doctor_id" required>
                                                @foreach ($doctors as $id => $name)
                                                    <option value="{{ $id }}" {{ old('doctor_id', $prescription->doctor_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                                                @endforeach
                                            </select>
                                            @error('doctor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="treatment_plan_id">ربط بخطة علاجية (اختياري)</label>
                                            <select class="form-control" name="treatment_plan_id" id="treatment_plan_id">
                                                <option value="">-- اختر خطة --</option>
                                                @foreach ($treatmentPlans as $id => $diagnosis)
                                                    <option value="{{ $id }}" {{ old('treatment_plan_id', $prescription->treatment_plan_id) == $id ? 'selected' : '' }}>
                                                        {{ \Illuminate\Support\Str::limit($diagnosis, 70) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="status">الحالة <span class="text-danger">*</span></label>
                                            <select class="form-control" name="status" id="status" required>
                                                @foreach($statuses as $key => $value)
                                                    <option value="{{ $key }}" {{ old('status', $prescription->status) == $key ? 'selected' : '' }}>{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="notes">ملاحظات عامة</label>
                                            <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes', $prescription->notes) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                {{-- ===================================================== --}}
                                {{--           END: Main Prescription Form Fields          --}}
                                {{-- ===================================================== --}}


                                {{-- ================================================ --}}
                                {{--           BEGIN: Prescription Items Section      --}}
                                {{-- ================================================ --}}
                                <h4 class="mb-3">بنود الوصفة</h4>
                                <div id="prescription-items-container">
                                    @php
                                        $items = old('items', $prescription->items->toArray());
                                    @endphp
                                    @foreach($items as $index => $item)
                                        @php $item = (object) $item; @endphp
                                        {{-- This is the content of _item_row.blade.php --}}
                                        <div class="row item-row border rounded p-3 mb-3 position-relative">
                                            <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id ?? '' }}">
                                            <div class="position-absolute top-0 end-0 p-2"><button type="button" class="btn btn-danger btn-sm remove-item-btn" title="حذف الدواء"><i class="fe fe-trash"></i></button></div>
                                            <div class="col-md-6"><div class="form-group"><label>الدواء <span class="text-danger">*</span></label><select name="items[{{ $index }}][medication_id]" class="form-control medication-select @error('items.'.$index.'.medication_id') is-invalid @enderror"><option value="">-- اختر من القائمة --</option>@foreach($medications as $medication)<option value="{{ $medication->id }}" {{ ($item->medication_id ?? null) == $medication->id ? 'selected' : '' }}>{{ $medication->name }} ({{ $medication->strength }}) - {{ $medication->form }}</option>@endforeach<option value="manual" {{ (!($item->medication_id ?? null) && ($item->medication_name_manual ?? null)) ? 'selected' : '' }}>-- إدخال يدوي --</option></select>@error('items.'.$index.'.medication_id')<div class="invalid-feedback">{{ $message }}</div>@enderror</div></div>
                                            <div class="col-md-6 medication-manual-input" style="display: none;"><div class="form-group"><label>اسم الدواء (يدوي) <span class="text-danger">*</span></label><input type="text" name="items[{{ $index }}][medication_name_manual]" class="form-control @error('items.'.$index.'.medication_name_manual') is-invalid @enderror" value="{{ $item->medication_name_manual ?? '' }}">@error('items.'.$index.'.medication_name_manual')<div class="invalid-feedback">{{ $message }}</div>@enderror</div></div>
                                            <div class="col-md-3"><div class="form-group"><label>الجرعة <span class="text-danger">*</span></label><input type="text" name="items[{{ $index }}][dosage]" class="form-control @error('items.'.$index.'.dosage') is-invalid @enderror" value="{{ $item->dosage ?? '' }}" required>@error('items.'.$index.'.dosage')<div class="invalid-feedback">{{ $message }}</div>@enderror</div></div>
                                            <div class="col-md-3"><div class="form-group"><label>التكرار <span class="text-danger">*</span></label><input type="text" name="items[{{ $index }}][frequency]" class="form-control @error('items.'.$index.'.frequency') is-invalid @enderror" value="{{ $item->frequency ?? '' }}" required>@error('items.'.$index.'.frequency')<div class="invalid-feedback">{{ $message }}</div>@enderror</div></div>
                                            <div class="col-md-3"><div class="form-group"><label>المدة <span class="text-danger">*</span></label><input type="text" name="items[{{ $index }}][duration]" class="form-control @error('items.'.$index.'.duration') is-invalid @enderror" value="{{ $item->duration ?? '' }}" required>@error('items.'.$index.'.duration')<div class="invalid-feedback">{{ $message }}</div>@enderror</div></div>
                                            <div class="col-md-3"><div class="form-group"><label>الكمية الموصوفة</label><input type="number" name="items[{{ $index }}][quantity_prescribed]" class="form-control @error('items.'.$index.'.quantity_prescribed') is-invalid @enderror" value="{{ $item->quantity_prescribed ?? '' }}" min="0">@error('items.'.$index.'.quantity_prescribed')<div class="invalid-feedback">{{ $message }}</div>@enderror</div></div>
                                            <div class="col-md-12"><div class="form-group"><label>تعليمات إضافية</label><textarea name="items[{{ $index }}][instructions]" class="form-control @error('items.'.$index.'.instructions') is-invalid @enderror" rows="2">{{ $item->instructions ?? '' }}</textarea>@error('items.'.$index.'.instructions')<div class="invalid-feedback">{{ $message }}</div>@enderror</div></div>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" class="btn btn-secondary btn-sm" id="add-item-btn"><i class="fe fe-plus"></i> إضافة دواء آخر</button>
                                {{-- ============================================== --}}
                                {{--           END: Prescription Items Section      --}}
                                {{-- ============================================== --}}


                                <div class="text-end mt-4">
                                    <a href="{{ route('clinical.prescriptions.show', [$patient->id, $prescription->id]) }}" class="btn btn-secondary">إلغاء</a>
                                    <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
{{-- JavaScript for this page is identical to the create page --}}
<script>
$(document).ready(function() {
    let itemIndex = $('#prescription-items-container .item-row').length;
    function toggleManualInput(selectElement) { /* ... (same function as create) ... */ }
    $('#add-item-btn').click(function() { /* ... (same function as create) ... */ });
    $('#prescription-items-container').on('click', '.remove-item-btn', function() { /* ... (same function as create) ... */ });
    $('#prescription-items-container').on('change', '.medication-select', function() { /* ... (same function as create) ... */ });
    $('.medication-select').each(function() { toggleManualInput(this); });

    // For brevity, I'll write the functions in full again
    function toggleManualInput(selectElement) {
        const row = $(selectElement).closest('.item-row');
        const manualInputContainer = row.find('.medication-manual-input');
        const manualInput = manualInputContainer.find('input');
        const medicationSelect = row.find('.medication-select');

        if ($(selectElement).val() === 'manual') {
            manualInputContainer.show();
            manualInput.prop('required', true);
            medicationSelect.prop('required', false);
        } else {
            manualInputContainer.hide();
            manualInput.prop('required', false).val('');
            medicationSelect.prop('required', true);
        }
    }

    $('#add-item-btn').click(function() {
        let newRowHtml = `
            <div class="row item-row border rounded p-3 mb-3 position-relative">
                <input type="hidden" name="items[__INDEX__][id]" value="">
                <div class="position-absolute top-0 end-0 p-2"><button type="button" class="btn btn-danger btn-sm remove-item-btn" title="حذف الدواء"><i class="fe fe-trash"></i></button></div>
                <div class="col-md-6"><div class="form-group"><label>الدواء <span class="text-danger">*</span></label><select name="items[__INDEX__][medication_id]" class="form-control medication-select"><option value="">-- اختر من القائمة --</option>@foreach($medications as $medication)<option value="{{ $medication->id }}">{{ $medication->name }} ({{ $medication->strength }}) - {{ $medication->form }}</option>@endforeach<option value="manual">-- إدخال يدوي --</option></select></div></div>
                <div class="col-md-6 medication-manual-input" style="display: none;"><div class="form-group"><label>اسم الدواء (يدوي) <span class="text-danger">*</span></label><input type="text" name="items[__INDEX__][medication_name_manual]" class="form-control"></div></div>
                <div class="col-md-3"><div class="form-group"><label>الجرعة <span class="text-danger">*</span></label><input type="text" name="items[__INDEX__][dosage]" class="form-control" required></div></div>
                <div class="col-md-3"><div class="form-group"><label>التكرار <span class="text-danger">*</span></label><input type="text" name="items[__INDEX__][frequency]" class="form-control" required></div></div>
                <div class="col-md-3"><div class="form-group"><label>المدة <span class="text-danger">*</span></label><input type="text" name="items[__INDEX__][duration]" class="form-control" required></div></div>
                <div class="col-md-3"><div class="form-group"><label>الكمية الموصوفة</label><input type="number" name="items[__INDEX__][quantity_prescribed]" class="form-control" min="0"></div></div>
                <div class="col-md-12"><div class="form-group"><label>تعليمات إضافية</label><textarea name="items[__INDEX__][instructions]" class="form-control" rows="2"></textarea></div></div>
            </div>
        `;
        let template = newRowHtml.replace(/__INDEX__/g, itemIndex);
        $('#prescription-items-container').append(template);
        itemIndex++;
    });
});
</script>
@endpush