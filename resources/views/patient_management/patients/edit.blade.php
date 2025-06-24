@php $page = 'edit-patient'; @endphp
@extends('layout.mainlayout')
@section('title', 'تعديل بيانات المريض')

@section('content')
    <div class="page-wrapper">
        <div class="content">
            @component('components.page-header')
                @slot('title')
                    المرضى
                @endslot
                @slot('li_1')
                    تعديل بيانات المريض: {{ $patient->full_name }}
                @endslot
            @endcomponent

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('patient_management.patients.update', $patient->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-heading">
                                            <h4>بيانات المريض</h4>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <div class="form-group local-forms">
                                            <label>الاسم الكامل <span class="login-danger">*</span></label>
                                            <input class="form-control @error('full_name') is-invalid @enderror" type="text" name="full_name" value="{{ old('full_name', $patient->full_name) }}">
                                            @error('full_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <div class="form-group local-forms">
                                            <label>رقم الملف <span class="login-danger">*</span></label>
                                            <input class="form-control @error('file_number') is-invalid @enderror" type="text" name="file_number" value="{{ old('file_number', $patient->file_number) }}">
                                            @error('file_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <div class="form-group local-forms">
                                            <label>الصورة الشخصية الحالية</label>
                                            @if($patient->profile_image_path)
                                                <img src="{{ Storage::url($patient->profile_image_path) }}" alt="الصورة الشخصية" width="100" class="mb-2">
                                            @else
                                                <p>لا توجد صورة حالية.</p>
                                            @endif
                                            <label>تغيير الصورة الشخصية (اختياري)</label>
                                            <input class="form-control @error('profile_image') is-invalid @enderror" type="file" name="profile_image" accept="image/*">
                                            @error('profile_image')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-xl-3">
                                        <div class="form-group local-forms">
                                            <label>العمر التقريبي</label>
                                            <input class="form-control @error('approximate_age') is-invalid @enderror" type="number" name="approximate_age" value="{{ old('approximate_age', $patient->approximate_age) }}">
                                            @error('approximate_age')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-xl-3">
    <div class="form-group local-forms">
        <label>النوع (الجنس)</label>
        <select class="form-select @error('gender') is-invalid @enderror" name="gender">
            <option value="">-- اختر النوع --</option>
            <option value="male" {{ old('gender', $patient->gender) == 'male' ? 'selected' : '' }}>ذكر</option>
            <option value="female" {{ old('gender', $patient->gender) == 'female' ? 'selected' : '' }}>أنثى</option>
            <option value="other" {{ old('gender', $patient->gender) == 'other' ? 'selected' : '' }}>آخر</option>
            <option value="unknown" {{ old('gender', $patient->gender) == 'unknown' ? 'selected' : '' }}>غير معروف</option>
        </select>
        @error('gender')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
                                    <div class="col-12 col-md-6 col-xl-3">
                                        <div class="form-group local-forms">
                                            <label>المحافظة</label>
                                            <input class="form-control @error('province') is-invalid @enderror" type="text" name="province" value="{{ old('province', $patient->province) }}">
                                            @error('province')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                     <div class="col-12 col-md-6 col-xl-3">
                                        <div class="form-group local-forms cal-icon">
                                            <label>تاريخ الوصول <span class="login-danger">*</span></label>
                                            <input class="form-control datetimepicker @error('arrival_date') is-invalid @enderror" type="text" name="arrival_date" value="{{ old('arrival_date', $patient->arrival_date ? \Carbon\Carbon::parse($patient->arrival_date)->format('Y-m-d') : '') }}">
                                            @error('arrival_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-xl-3">
                                        <div class="form-group local-forms">
                                            <label>حالة المريض <span class="login-danger">*</span></label>
                                            <select class="form-select @error('status') is-invalid @enderror" name="status">
                                                <option value="active" {{ old('status', $patient->status) == 'active' ? 'selected' : '' }}>نشط</option>
                                                <option value="discharged" {{ old('status', $patient->status) == 'discharged' ? 'selected' : '' }}>خروج</option>
                                                <option value="deceased" {{ old('status', $patient->status) == 'deceased' ? 'selected' : '' }}>متوفى</option>
                                                <option value="transferred" {{ old('status', $patient->status) == 'transferred' ? 'selected' : '' }}>محول</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-12">
                                        <div class="form-group local-forms">
                                            <label>الحالة عند الوصول</label>
                                            <textarea class="form-control @error('condition_on_arrival') is-invalid @enderror" name="condition_on_arrival" rows="3" cols="30">{{ old('condition_on_arrival', $patient->condition_on_arrival) }}</textarea>
                                            @error('condition_on_arrival')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="doctor-submit text-end">
                                            <button type="submit" class="btn btn-primary submit-form me-2">حفظ التعديلات</button>
                                            <a href="{{ route('patient_management.patients.show', $patient->id) }}" class="btn btn-secondary cancel-form">إلغاء</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @component('components.notification-box')
        @endcomponent
    </div>
@endsection