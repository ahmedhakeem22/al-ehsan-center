@csrf
<div class="row">
    {{-- Name --}}
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="name" class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                   value="{{ old('name', $user->name ?? '') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- Username --}}
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="username" class="form-label">اسم المستخدم <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username"
                   value="{{ old('username', $user->username ?? '') }}" required>
            @error('username')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- Email --}}
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="email" class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                   value="{{ old('email', $user->email ?? '') }}" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- Role --}}
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="role_id" class="form-label">الدور (الصلاحية) <span class="text-danger">*</span></label>
            <select class="form-control select @error('role_id') is-invalid @enderror" id="role_id" name="role_id" required>
                 <option value="" disabled selected>-- اختر دوراً --</option>
                @foreach($roles as $id => $name)
                    <option value="{{ $id }}" @selected(old('role_id', $user->role_id ?? '') == $id)>{{ $name }}</option>
                @endforeach
            </select>
            @error('role_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- Password --}}
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="password" class="form-label">
                كلمة المرور 
                @if(!isset($user))
                    <span class="text-danger">*</span>
                @endif
            </label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" {{ !isset($user) ? 'required' : '' }}>
            @if(isset($user))
                <small class="form-text text-muted">اتركه فارغاً لعدم تغيير كلمة المرور.</small>
            @endif
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- Password Confirmation --}}
     <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="password_confirmation" class="form-label">
                تأكيد كلمة المرور 
                @if(!isset($user))
                    <span class="text-danger">*</span>
                @endif
            </label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" {{ !isset($user) ? 'required' : '' }}>
        </div>
    </div>

    {{-- Is Active Switch --}}
     <div class="col-md-12">
        <div class="form-group mb-3">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $user->is_active ?? true))>
                <label class="form-check-label" for="is_active">الحساب نشط</label>
            </div>
        </div>
    </div>
</div>

{{-- Buttons --}}
<div class="text-end mt-4">
    <button type="submit" class="btn btn-primary submit-form me-2">
        {{ isset($user) ? 'تحديث المستخدم' : 'إنشاء المستخدم' }}
    </button>
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary cancel-form">إلغاء</a>
</div>