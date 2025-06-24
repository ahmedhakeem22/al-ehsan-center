@csrf
<div class="row">
    <div class="col-md-12">
        <div class="form-group local-forms">
            <label for="name">اسم الطابق <span class="login-danger">*</span></label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                   value="{{ old('name', $floor->name ?? '') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group local-forms">
            <label for="description">الوصف (اختياري)</label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                      rows="3">{{ old('description', $floor->description ?? '') }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="text-end mt-4">
    <button type="submit" class="btn btn-primary submit-form me-2">
        {{ isset($floor) ? 'تحديث الطابق' : 'إنشاء الطابق' }}
    </button>
    <a href="{{ route('admin.settings.floors.index') }}" class="btn btn-secondary cancel-form">إلغاء</a>
</div>