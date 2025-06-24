@csrf
<div class="row">
    <div class="col-md-6">
        <div class="form-group local-forms">
            <label for="floor_id">الطابق <span class="login-danger">*</span></label>
            <select name="floor_id" id="floor_id" class="form-select @error('floor_id') is-invalid @enderror" required>
                <option value="">-- اختر الطابق --</option>
                @foreach ($floors as $id => $name)
                    <option value="{{ $id }}" {{ old('floor_id', isset($room) ? $room->floor_id : '') == $id ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
            @error('floor_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group local-forms">
            <label for="room_number">رقم/اسم الغرفة <span class="login-danger">*</span></label>
            <input type="text" class="form-control @error('room_number') is-invalid @enderror" id="room_number" name="room_number"
                   value="{{ old('room_number', $room->room_number ?? '') }}" required>
            @error('room_number')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group local-forms">
            <label for="capacity">سعة الغرفة (عدد الأسرة) <span class="login-danger">*</span></label>
            <input type="number" class="form-control @error('capacity') is-invalid @enderror" id="capacity" name="capacity"
                   value="{{ old('capacity', $room->capacity ?? 1) }}" required min="1">
            @error('capacity')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="text-end mt-4">
    <button type="submit" class="btn btn-primary submit-form me-2">
        {{ isset($room) ? 'تحديث الغرفة' : 'إنشاء الغرفة' }}
    </button>
    <a href="{{ route('admin.settings.rooms.index') }}" class="btn btn-secondary cancel-form">إلغاء</a>
</div>