@csrf
<div class="row">
    {{-- Personal Information --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h5 class="card-title">المعلومات الشخصية والوظيفية</h5></div>
            <div class="card-body">

                <div class="form-group mb-3">
                    <label for="full_name" class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('full_name') is-invalid @enderror" id="full_name" name="full_name" value="{{ old('full_name', $employee->full_name ?? '') }}" required>
                    @error('full_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="job_title" class="form-label">المسمى الوظيفي <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('job_title') is-invalid @enderror" id="job_title" name="job_title" value="{{ old('job_title', $employee->job_title ?? '') }}" required>
                    @error('job_title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="phone_number" class="form-label">رقم الهاتف</label>
                    <input type="tel" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{ old('phone_number', $employee->phone_number ?? '') }}">
                    @error('phone_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="salary" class="form-label">الراتب</label>
                    <input type="number" step="0.01" class="form-control @error('salary') is-invalid @enderror" id="salary" name="salary" value="{{ old('salary', $employee->salary ?? '') }}">
                    @error('salary') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="joining_date" class="form-label">تاريخ التعيين</label>
                    <input type="date" class="form-control @error('joining_date') is-invalid @enderror" id="joining_date" name="joining_date" value="{{ old('joining_date', isset($employee->joining_date) ? $employee->joining_date->format('Y-m-d') : '') }}">
                    @error('joining_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="date_of_birth" class="form-label">تاريخ الميلاد</label>
                    <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', isset($employee->date_of_birth) ? $employee->date_of_birth->format('Y-m-d') : '') }}">
                    @error('date_of_birth') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="qualification" class="form-label">المؤهل العلمي</label>
                    <input type="text" class="form-control @error('qualification') is-invalid @enderror" id="qualification" name="qualification" value="{{ old('qualification', $employee->qualification ?? '') }}">
                     @error('qualification') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="marital_status" class="form-label">الحالة الاجتماعية</label>
                    <select class="form-control select @error('marital_status') is-invalid @enderror" id="marital_status" name="marital_status">
                        <option value="" disabled selected>-- اختر --</option>
                        <option value="أعزب" @selected(old('marital_status', $employee->marital_status ?? '') == 'أعزب')>أعزب</option>
                        <option value="متزوج" @selected(old('marital_status', $employee->marital_status ?? '') == 'متزوج')>متزوج</option>
                        <option value="آخر" @selected(old('marital_status', $employee->marital_status ?? '') == 'آخر')>آخر</option>
                    </select>
                    @error('marital_status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="address" class="form-label">العنوان</label>
                    <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address', $employee->address ?? '') }}</textarea>
                    @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="profile_picture" class="form-label">الصورة الشخصية</label>
                    <input type="file" class="form-control @error('profile_picture') is-invalid @enderror" id="profile_picture" name="profile_picture" accept="image/*">
                     @if(isset($employee) && $employee->profile_picture_path)
                        <img src="{{ Storage::url($employee->profile_picture_path) }}" alt="Profile" class="mt-2 img-thumbnail" width="100">
                    @endif
                    @error('profile_picture') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- User Account Management --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h5 class="card-title">إدارة حساب المستخدم</h5></div>
            <div class="card-body">

                {{-- In Create Mode --}}
                @if(!isset($employee))
                    <div class="form-group mb-3">
                        <label for="user_id" class="form-label">ربط بحساب مستخدم حالي (اختياري)</label>
                        <select name="user_id" id="user_id" class="form-control select @error('user_id') is-invalid @enderror">
                            <option value="">-- لا تربط بحساب --</option>
                            @foreach($unlinkedUsers as $id => $name)
                            <option value="{{ $id }}" @selected(old('user_id') == $id)>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <hr>
                    <div class="form-group form-check form-switch mb-3">
                        <input type="checkbox" class="form-check-input" id="create_new_user" name="create_new_user" value="1" @checked(old('create_new_user'))>
                        <label class="form-check-label" for="create_new_user">أو إنشاء حساب مستخدم جديد لهذا الموظف</label>
                    </div>

                    <div id="new_user_fields" style="{{ old('create_new_user') ? '' : 'display:none;' }}">
                        <div class="form-group mb-3">
                            <label for="username" class="form-label">اسم المستخدم <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}">
                            @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="email" class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                         <div class="form-group mb-3">
                            <label for="role_id" class="form-label">الدور <span class="text-danger">*</span></label>
                            <select name="role_id" class="form-control select @error('role_id') is-invalid @enderror">
                                <option value="" disabled selected>-- اختر دور --</option>
                                @foreach($rolesForNewUser as $id => $name)
                                <option value="{{ $id }}" @selected(old('role_id') == $id)>{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('role_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="password" class="form-label">كلمة المرور <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                             @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="password_confirmation" class="form-label">تأكيد كلمة المرور <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                    </div>
                @else {{-- In Edit Mode --}}
                    <div class="form-group mb-3">
                        <label for="user_id" class="form-label">الحساب المربوط</label>
                        <select name="user_id" id="user_id" class="form-control select @error('user_id') is-invalid @enderror">
                            <option value="">-- فصل الحساب --</option>
                            @foreach($availableUsers as $id => $name)
                            <option value="{{ $id }}" @selected(old('user_id', $employee->user_id) == $id)>{{ $name }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">يمكنك تغيير الحساب المربوط أو فصله.</small>
                         @error('user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div id="linked_user_fields" style="{{ old('user_id', $employee->user_id) ? '' : 'display:none;' }}">
                         <hr>
                        <h5>تعديل بيانات الحساب المربوط</h5>
                         <div class="form-group mb-3">
                            <label for="user_role_id" class="form-label">دور المستخدم</label>
                            <select name="user_role_id" class="form-control select @error('user_role_id') is-invalid @enderror">
                                @foreach($roles as $id => $name)
                                <option value="{{ $id }}" @selected(old('user_role_id', $employee->user?->role_id) == $id)>{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('user_role_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                         <div class="form-group form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="user_is_active" name="user_is_active" value="1" @checked(old('user_is_active', $employee->user?->is_active ?? true))>
                            <label class="form-check-label" for="user_is_active">الحساب نشط</label>
                        </div>
                        <div class="form-group mb-3">
                            <label for="user_new_password" class="form-label">كلمة مرور جديدة (اتركه فارغاً لعدم التغيير)</label>
                            <input type="password" name="user_new_password" class="form-control @error('user_new_password') is-invalid @enderror">
                            @error('user_new_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="user_new_password_confirmation" class="form-label">تأكيد كلمة المرور الجديدة</label>
                            <input type="password" name="user_new_password_confirmation" class="form-control">
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="text-end mt-4">
    <button type="submit" class="btn btn-primary submit-form me-2">
        {{ isset($employee) ? 'تحديث الموظف' : 'إنشاء الموظف' }}
    </button>
    <a href="{{ route('hr.employees.index') }}" class="btn btn-secondary cancel-form">إلغاء</a>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const createUserCheckbox = document.getElementById('create_new_user');
    const newUserFields = document.getElementById('new_user_fields');
    const userIdSelectCreate = document.querySelector('select[name="user_id"]');

    function toggleNewUserFields() {
        if (createUserCheckbox && createUserCheckbox.checked) {
            newUserFields.style.display = 'block';
            if (userIdSelectCreate) {
                userIdSelectCreate.value = '';
                // If using Select2, you might need to trigger a change
                // $(userIdSelectCreate).val(null).trigger('change');
            }
        } else if(newUserFields) {
            newUserFields.style.display = 'none';
        }
    }
    if (createUserCheckbox) {
        createUserCheckbox.addEventListener('change', toggleNewUserFields);
    }
    
    // For Edit form - user linking
    const userIdSelectEdit = document.querySelector('select[name="user_id"]');
    const linkedUserFields = document.getElementById('linked_user_fields');

    function toggleLinkedUserFields() {
        if (linkedUserFields) {
            if (userIdSelectEdit && userIdSelectEdit.value) {
                linkedUserFields.style.display = 'block';
            } else {
                linkedUserFields.style.display = 'none';
            }
        }
    }

    if (userIdSelectEdit) {
        userIdSelectEdit.addEventListener('change', toggleLinkedUserFields);
    }
});
</script>
@endpush