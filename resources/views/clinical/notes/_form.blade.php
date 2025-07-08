@csrf
<div class="row">
    <div class="col-md-12 mb-3">
        <label for="note_type" class="form-label">نوع الملاحظة <span class="text-danger">*</span></label>
        <select name="note_type" id="note_type" class="form-select @error('note_type') is-invalid @enderror" required>
            <option value="">اختر نوع الملاحظة</option>
            @foreach($allowedNoteTypes as $key => $value)
 <option value="{{ $key }}" @selected(old('note_type', $note->note_type->value ?? '') == $key)>
                    {{ $value }}
                </option>            @endforeach
        </select>
        @error('note_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12 mb-3">
        <label for="content" class="form-label">محتوى الملاحظة <span class="text-danger">*</span></label>
        <textarea name="content" id="content" class="form-control @error('content') is-invalid @enderror" rows="8" required>{{ old('content', $note->content ?? '') }}</textarea>
        @error('content') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- You can add a field for replies here if needed --}}
    {{--
    <div class="col-12 mb-3">
        <label for="related_to_note_id" class="form-label">رد على ملاحظة (اختياري)</label>
        <select name="related_to_note_id" id="related_to_note_id" class="form-select">
            <option value="">لا يوجد</option>
            // Populate with other notes for this patient
        </select>
    </div>
    --}}
</div>