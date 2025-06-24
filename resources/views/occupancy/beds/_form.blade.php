@csrf
<div class="row">
    <div class="col-md-6">
        <div class="form-group local-forms">
            <label for="room_id">الغرفة <span class="login-danger">*</span></label>
            <select name="room_id" id="room_id" class="form-select @error('room_id') is-invalid @enderror" required>
                <option value="">-- اختر الغرفة --</option>
                @foreach ($rooms as $id => $roomName)
                    <option value="{{ $id }}" {{ old('room_id', isset($bed) ? $bed->room_id : '') == $id ? 'selected' : '' }}>
                        {{ $roomName }}
                    </option>
                @endforeach
            </select>
            @error('room_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group local-forms">
            <label for="bed_number">رقم السرير <span class="login-danger">*</span></label>
            <input type="text" class="form-control @error('bed_number') is-invalid @enderror" id="bed_number" name="bed_number"
                   value="{{ old('bed_number', $bed->bed_number ?? '') }}" required>
            @error('bed_number')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group local-forms">
            <label for="status">حالة السرير <span class="login-danger">*</span></label>
            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                @php
                    $statuses = $bedStatuses ?? ['vacant' => 'شاغر', 'occupied' => 'مشغول', 'reserved' => 'محجوز', 'out_of_service' => 'خارج الخدمة'];
                @endphp
                @foreach ($statuses as $key => $value)
                    <option value="{{ $key }}" {{ old('status', isset($bed) ? $bed->status : 'vacant') == $key ? 'selected' : '' }}>
                        {{ $value }}
                    </option>
                @endforeach
            </select>
            @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="text-end mt-4">
    <button type="submit" class="btn btn-primary submit-form me-2">
        {{ isset($bed) ? 'تحديث السرير' : 'إنشاء السرير' }}
    </button>
    <a href="{{ route('occupancy.beds.index') }}" class="btn btn-secondary cancel-form">إلغاء</a>
</div>