@csrf
<div class="row">
    <div class="col-md-12">
        <div class="form-group local-forms">
            <label for="document_type">نوع المستند <span class="login-danger">*</span></label>
            <input type="text" list="document_types_list" class="form-control @error('document_type') is-invalid @enderror" id="document_type" name="document_type" value="{{ old('document_type') }}" required>
            <datalist id="document_types_list">
                @foreach($documentTypes as $type)
                    <option value="{{ $type }}">
                @endforeach
            </datalist>
            @error('document_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label for="document_file">ملف المستند <span class="login-danger">*</span></label>
            <input type="file" class="form-control @error('document_file') is-invalid @enderror" id="document_file" name="document_file" required>
            <small class="form-text text-muted">الصيغ المسموحة: PDF, DOC, DOCX, JPG, PNG. الحجم الأقصى: 10MB.</small>
            @error('document_file') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group local-forms">
            <label for="description">الوصف (اختياري)</label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>
</div>

<div class="text-end mt-4">
    <button type="submit" class="btn btn-primary submit-form me-2">رفع المستند</button>
    <a href="{{ route('hr.documents.index', $employee->id) }}" class="btn btn-secondary cancel-form">إلغاء</a>
</div>