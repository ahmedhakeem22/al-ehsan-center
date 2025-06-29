@csrf
<div class="row">
    <div class="col-md-12">
        <div class="form-group local-forms">
            <label for="name">اسم المناوبة <span class="login-danger">*</span></label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                   value="{{ old('name', $shiftDefinition->name ?? '') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group local-forms">
            <label for="start_time">وقت البدء <span class="login-danger">*</span></label>
            <input type="time" class="form-control @error('start_time') is-invalid @enderror" id="start_time" name="start_time"
                   value="{{ old('start_time', $shiftDefinition->start_time ?? '') }}" required>
            @error('start_time')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group local-forms">
            <label for="end_time">وقت الانتهاء <span class="login-danger">*</span></label>
            <input type="time" class="form-control @error('end_time') is-invalid @enderror" id="end_time" name="end_time"
                   value="{{ old('end_time', $shiftDefinition->end_time ?? '') }}" required>
            @error('end_time')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group local-forms">
            <label for="duration_hours">عدد الساعات <span class="login-danger">*</span></label>
            <input type="number" step="0.1" class="form-control @error('duration_hours') is-invalid @enderror" id="duration_hours" name="duration_hours"
                   value="{{ old('duration_hours', $shiftDefinition->duration_hours ?? '') }}" required>
            @error('duration_hours')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
     <div class="col-md-6">
        <div class="form-group local-forms">
            <label for="color_code">اللون</label>
            <input type="color" class="form-control form-control-color @error('color_code') is-invalid @enderror" id="color_code" name="color_code"
                   value="{{ old('color_code', $shiftDefinition->color_code ?? '#55ce63') }}">
            @error('color_code')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="text-end mt-4">
    <button type="submit" class="btn btn-primary submit-form me-2">
        {{ isset($shiftDefinition) ? 'تحديث المناوبة' : 'إنشاء المناوبة' }}
    </button>
    <a href="{{ route('hr.shift_definitions.index') }}" class="btn btn-secondary cancel-form">إلغاء</a>
</div>