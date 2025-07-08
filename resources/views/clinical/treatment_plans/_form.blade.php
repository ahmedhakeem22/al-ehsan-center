@csrf
<div class="row">
    <div class="col-md-12 mb-3">
        <label for="diagnosis" class="form-label">التشخيص الأساسي <span class="text-danger">*</span></label>
        <input type="text" id="diagnosis" name="diagnosis" class="form-control @error('diagnosis') is-invalid @enderror"
               value="{{ old('diagnosis', $plan->diagnosis ?? '') }}" required>
        @error('diagnosis') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="doctor_id" class="form-label">الطبيب المسؤول <span class="text-danger">*</span></label>
        <select name="doctor_id" id="doctor_id" class="form-select @error('doctor_id') is-invalid @enderror" required>
            <option value="">اختر الطبيب</option>
            @foreach($doctors as $id => $name)
                <option value="{{ $id }}" @selected(old('doctor_id', $plan->doctor_id ?? auth()->id()) == $id)>{{ $name }}</option>
            @endforeach
        </select>
        @error('doctor_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="status" class="form-label">حالة الخطة <span class="text-danger">*</span></label>
        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
            @foreach($statuses as $key => $value)
                <option value="{{ $key }}" @selected(old('status', $plan->status ?? 'active') == $key)>{{ $value }}</option>
            @endforeach
        </select>
        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="start_date" class="form-label">تاريخ البدء <span class="text-danger">*</span></label>
        <input type="date" id="start_date" name="start_date" class="form-control @error('start_date') is-invalid @enderror"
               value="{{ old('start_date', isset($plan) ? $plan->start_date->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
        @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="end_date" class="form-label">تاريخ الانتهاء المتوقع</label>
        <input type="date" id="end_date" name="end_date" class="form-control @error('end_date') is-invalid @enderror"
               value="{{ old('end_date', isset($plan) && $plan->end_date ? $plan->end_date->format('Y-m-d') : '') }}">
        @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12 mb-3">
        <label for="plan_details" class="form-label">تفاصيل الخطة العلاجية (الأهداف، الإجراءات، إلخ)</label>
        <textarea name="plan_details" id="plan_details" class="form-control @error('plan_details') is-invalid @enderror" rows="6">{{ old('plan_details', $plan->plan_details ?? '') }}</textarea>
        @error('plan_details') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>