@csrf
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="name" class="form-label">اسم الفحص <span class="text-danger">*</span></label>
        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $availableLabTest->name ?? '') }}" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="code" class="form-label">الرمز (Code)</label>
        <input type="text" id="code" name="code" class="form-control @error('code') is-invalid @enderror"
               value="{{ old('code', $availableLabTest->code ?? '') }}">
        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="reference_range" class="form-label">المدى المرجعي (Reference Range)</label>
        <input type="text" id="reference_range" name="reference_range" class="form-control @error('reference_range') is-invalid @enderror"
               value="{{ old('reference_range', $availableLabTest->reference_range ?? '') }}">
        @error('reference_range') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="cost" class="form-label">التكلفة</label>
        <input type="number" id="cost" name="cost" class="form-control @error('cost') is-invalid @enderror"
               value="{{ old('cost', $availableLabTest->cost ?? '0.00') }}" step="0.01" min="0">
        @error('cost') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-12 mb-3">
        <label for="description" class="form-label">الوصف</label>
        <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"
                  rows="4">{{ old('description', $availableLabTest->description ?? '') }}</textarea>
        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>