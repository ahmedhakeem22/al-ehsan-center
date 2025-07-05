@csrf

{{-- هذا السطر ضروري لصفحة التعديل لتحديد نوع الطلب كـ PUT --}}
@if(isset($shiftDefinition))
    @method('PUT')
@endif

<div class="row">
    {{-- اسم المناوبة --}}
    <div class="col-md-6">
        <div class="form-group">
            <label for="name">اسم المناوبة <span class="text-danger">*</span></label>
            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $shiftDefinition->name ?? '') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- المدة بالساعات --}}
    <div class="col-md-6">
        <div class="form-group">
            <label for="duration_hours">المدة (بالساعات) <span class="text-danger">*</span></label>
            <input type="number" id="duration_hours" name="duration_hours" class="form-control @error('duration_hours') is-invalid @enderror"
                   value="{{ old('duration_hours', $shiftDefinition->duration_hours ?? '') }}" required step="0.5" min="0.5" max="24">
            @error('duration_hours')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- وقت البدء --}}
    <div class="col-md-6">
        <div class="form-group">
            <label for="start_time">وقت البدء <span class="text-danger">*</span></label>
            <input type="time" id="start_time" name="start_time" class="form-control @error('start_time') is-invalid @enderror"
                   value="{{ old('start_time', isset($shiftDefinition) ? \Carbon\Carbon::parse($shiftDefinition->start_time)->format('H:i') : '') }}" required>
            @error('start_time')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- وقت الانتهاء --}}
    <div class="col-md-6">
        <div class="form-group">
            <label for="end_time">وقت الانتهاء <span class="text-danger">*</span></label>
            <input type="time" id="end_time" name="end_time" class="form-control @error('end_time') is-invalid @enderror"
                   value="{{ old('end_time', isset($shiftDefinition) ? \Carbon\Carbon::parse($shiftDefinition->end_time)->format('H:i') : '') }}" required>
            @error('end_time')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- لون المناوبة --}}
    <div class="col-md-6">
        <div class="form-group">
            <label for="color_code">لون المناوبة (للتقويم)</label>
            <input type="color" id="color_code" name="color_code" class="form-control form-control-color @error('color_code') is-invalid @enderror"
                   value="{{ old('color_code', $shiftDefinition->color_code ?? '#3498db') }}" title="اختر لون المناوبة">
            @error('color_code')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="text-end mt-4">
    <a href="{{ route('hr.shift_definitions.index') }}" class="btn btn-secondary me-2">إلغاء</a>
    <button type="submit" class="btn btn-primary">
        {{ isset($shiftDefinition) ? 'تحديث المناوبة' : 'حفظ المناوبة' }}
    </button>
</div>