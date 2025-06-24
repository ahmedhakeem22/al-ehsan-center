@php $page = 'add-patient'; @endphp
@extends('layout.mainlayout')
@section('title', 'إضافة مريض جديد')

@section('content')
    <div class="page-wrapper">
        <div class="content">
            @component('components.page-header')
                @slot('title')
                    المرضى
                @endslot
                @slot('li_1')
                    تسجيل مريض جديد
                @endslot
            @endcomponent

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
<form action="{{ route('patient_management.admissions.register') }}" method="POST" enctype="multipart/form-data">                                @csrf
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-heading">
                                            <h4>بيانات المريض الأساسية</h4>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-xl-6">
                                        <div class="form-group local-forms">
                                            <label>الاسم الكامل <span class="login-danger">*</span></label>
                                            <input class="form-control @error('full_name') is-invalid @enderror" type="text" name="full_name" value="{{ old('full_name') }}" placeholder="ادخل الاسم الكامل للمريض">
                                            @error('full_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-xl-6">
                                        <div class="form-group local-forms">
                                            <label>الصورة الشخصية <span class="login-danger">*</span></label>
                                            <input class="form-control @error('profile_image') is-invalid @enderror" type="file" name="profile_image" accept="image/*">
                                             @error('profile_image')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <div class="form-group local-forms">
                                            <label>العمر التقريبي</label>
                                            <input class="form-control @error('approximate_age') is-invalid @enderror" type="number" name="approximate_age" value="{{ old('approximate_age') }}" placeholder="ادخل العمر التقريبي">
                                            @error('approximate_age')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-xl-4">
    <div class="form-group local-forms">
        <label>النوع (الجنس)</label>
        <select class="form-select @error('gender') is-invalid @enderror" name="gender">
            <option value="">-- اختر النوع --</option>
            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>ذكر</option>
            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>أنثى</option>
            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>آخر</option>
            <option value="unknown" {{ old('gender') == 'unknown' ? 'selected' : '' }}>غير معروف</option>
        </select>
        @error('gender')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <div class="form-group local-forms">
                                            <label>المحافظة</label>
                                            <input class="form-control @error('province') is-invalid @enderror" type="text" name="province" value="{{ old('province') }}" placeholder="ادخل اسم المحافظة">
                                             @error('province')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <div class="form-group local-forms cal-icon">
                                            <label>تاريخ الوصول <span class="login-danger">*</span></label>
                                            <input class="form-control datetimepicker @error('arrival_date') is-invalid @enderror" type="text" name="arrival_date" value="{{ old('arrival_date') }}" placeholder="اختر تاريخ الوصول">
                                            @error('arrival_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-12">
                                        <div class="form-group local-forms">
                                            <label>الحالة عند الوصول</label>
                                            <textarea class="form-control @error('condition_on_arrival') is-invalid @enderror" name="condition_on_arrival" rows="3" cols="30" placeholder="صف الحالة العامة للمريض عند العثور عليه">{{ old('condition_on_arrival') }}</textarea>
                                            @error('condition_on_arrival')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                     <div class="col-12 col-md-6 col-xl-6">
                                        <div class="form-group local-forms">
                                            <label>رقم الملف (يدوي - اختياري)</label>
                                            <input class="form-control @error('file_number_manual') is-invalid @enderror" type="text" name="file_number_manual" value="{{ old('file_number_manual') }}" placeholder="اتركه فارغاً للتوليد التلقائي">
                                            @error('file_number_manual')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="doctor-submit text-end">
                                            <button type="submit" class="btn btn-primary submit-form me-2">تسجيل ومتابعة للتسكين</button>
                                            <a href="{{ route('patient_management.patients.index') }}" class="btn btn-secondary cancel-form">إلغاء</a>
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