@php $page = 'patients'; @endphp
@extends('layout.mainlayout')
@section('title', 'قائمة المرضى')

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">

        {{-- 1. رأس الصفحة --}}
        @component('components.page-header')
            @slot('title')
                المرضى
            @endslot
            @slot('li_1')
                قائمة المرضى
            @endslot
        @endcomponent

        {{-- 2. زر الإجراء الرئيسي (إضافة مريض) --}}
        <div class="row mb-4">
            <div class="col-md-12 text-end">
                <a href="{{ route('patient_management.admissions.register') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus me-1"></i> تسجيل دخول مريض جديد
                </a>
            </div>
        </div>

        {{-- 3. بطاقة الفلترة والبحث --}}
        <div class="card">
            <div class="card-header">
                <h5 class="card-title"><i class="fas fa-filter me-2"></i>فلترة البحث</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('patient_management.patients.index') }}" method="GET">
                    <div class="row align-items-end">
                        <div class="col-md-5">
                            <label for="search" class="form-label">بحث</label>
                            <input type="text" id="search" name="search" class="form-control" placeholder="بحث بالاسم, رقم الملف, المحافظة..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="status" class="form-label">الحالة</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">جميع الحالات</option>
                                @foreach($statuses as $key => $value)
                                    <option value="{{ $key }}" @selected(request('status') == $key)>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 d-flex">
                            <button type="submit" class="btn btn-primary flex-grow-1 me-2"><i class="fas fa-search"></i> بحث</button>
                            <a href="{{ route('patient_management.patients.index') }}" class="btn btn-outline-secondary flex-grow-1"><i class="fas fa-redo"></i> إعادة</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- 4. بطاقة عرض النتائج (الجدول) --}}
        <div class="row mt-4">
            <div class="col-sm-12">
                <div class="card card-table">
                    <div class="card-header">
                       <h5 class="card-title">قائمة المرضى ({{ $patients->total() }})</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-center mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>الصورة</th>
                                        <th>رقم الملف</th>
                                        <th>الاسم الكامل</th>
                                        <th>العمر</th>
                                        <th>المحافظة</th>
                                        <th>تاريخ الوصول</th>
                                        <th>الحالة</th>
                                        <th>السرير الحالي</th>
                                        <th class="text-end">إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($patients as $patient)
                                        <tr>
                                            <td>{{ $patients->firstItem() + $loop->index }}</td>
                                            <td>
                                                <a href="{{ route('patient_management.patients.show', $patient->id) }}">
                                                    <img width="40" height="40"
                                                         src="{{ $patient->profile_image_path ? Storage::url($patient->profile_image_path) : asset('assets/img/placeholder-user.png') }}"
                                                         class="rounded-circle" alt="{{ $patient->full_name }}">
                                                </a>
                                            </td>
                                            <td><span class="badge bg-light text-dark">{{ $patient->file_number }}</span></td>
                                            <td>
                                                <a href="{{ route('patient_management.patients.show', $patient->id) }}" class="text-dark fw-bold">{{ $patient->full_name }}</a>
                                            </td>
                                            <td>{{ $patient->approximate_age ?? 'N/A' }}</td>
                                            <td>{{ $patient->province ?? 'N/A' }}</td>
                                            <td>{{ $patient->arrival_date ? \Carbon\Carbon::parse($patient->arrival_date)->format('Y-m-d') : 'N/A' }}</td>
                                            <td>{!! $patient->status_badge !!}</td>
                                            <td>
                                                @if($patient->currentBed)
                                                    <span class="text-info">{{ $patient->currentBed->bed_number }} ({{ $patient->currentBed->room->room_number }})</span>
                                                @else
                                                    <span class="badge bg-secondary-light">غير مسكن</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <div class="dropdown dropdown-action">
                                                    <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a class="dropdown-item" href="{{ route('patient_management.patients.show', $patient->id) }}"><i class="fa-solid fa-eye m-r-5"></i> عرض الملف</a>
                                                        <a class="dropdown-item" href="{{ route('patient_management.patients.edit', $patient->id) }}"><i class="fa-solid fa-pen m-r-5"></i> تعديل البيانات</a>
                                                        <div class="dropdown-divider"></div>
                                                        <form action="{{ route('patient_management.patients.destroy', $patient->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا المريض؟ سيتم حذف جميع بياناته المتعلقة بشكل نهائي.');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger"><i class="fa fa-trash-alt m-r-5"></i> حذف</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-4">
                                                <h5>لا توجد نتائج تطابق بحثك</h5>
                                                <p>حاول تغيير كلمات البحث أو الفلترة.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- 5. كود الـ Pagination الصحيح --}}
                        @if ($patients->hasPages())
                        <div class="mt-4 d-flex justify-content-center">
                            {{-- هذا الكود سيعمل فقط إذا كنت تستخدم ->paginate() في المتحكم --}}
                            {{ $patients->appends(request()->query())->links('pagination::bootstrap-5') }}
                        </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>

    </div>
    @component('components.notification-box')
    @endcomponent
</div>
@endsection