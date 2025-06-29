@php $page = 'hr-employees-show'; @endphp
@extends('layout.mainlayout')
@section('title', 'ملف الموظف - ' . $employee->full_name)

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        @component('components.page-header')
            @slot('title')
                ملف الموظف
            @endslot
            @slot('li_1')
                 <a href="{{ route('hr.employees.index') }}">الموظفين</a>
            @endslot
            @slot('li_2')
                عرض التفاصيل
            @endslot
        @endcomponent

        <div class="card mb-0">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="profile-view">
                            <div class="profile-img-wrap">
                                <div class="profile-img">
                                    <a href="#"><img alt="" src="{{ $employee->profile_picture_path ? Storage::url($employee->profile_picture_path) : asset('assets/img/profiles/avatar-01.jpg') }}"></a>
                                </div>
                            </div>
                            <div class="profile-basic">
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="profile-info-left">
                                            <h3 class="user-name m-t-0 mb-0">{{ $employee->full_name }}</h3>
                                            <h6 class="text-muted">{{ $employee->job_title }}</h6>
                                            <div class="staff-id">رقم الموظف : {{ $employee->id }}</div>
                                            <div class="small doj text-muted">تاريخ الالتحاق : {{ $employee->joining_date?->format('d M Y') ?? '-' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        <ul class="personal-info">
                                            <li>
                                                <div class="title">رقم الهاتف:</div>
                                                <div class="text"><a href="tel:{{ $employee->phone_number }}">{{ $employee->phone_number ?? '-' }}</a></div>
                                            </li>
                                             @if($employee->user)
                                            <li>
                                                <div class="title">البريد الإلكتروني:</div>
                                                <div class="text"><a href="mailto:{{ $employee->user->email }}">{{ $employee->user->email }}</a></div>
                                            </li>
                                            @endif
                                            <li>
                                                <div class="title">تاريخ الميلاد:</div>
                                                <div class="text">{{ $employee->date_of_birth?->format('d M Y') ?? '-' }}</div>
                                            </li>
                                            <li>
                                                <div class="title">العنوان:</div>
                                                <div class="text">{{ $employee->address ?? '-' }}</div>
                                            </li>
                                            <li>
                                                <div class="title">الراتب:</div>
                                                <div class="text">{{ number_format($employee->salary, 2) }}</div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="pro-edit">
                                <a href="{{ route('hr.employees.edit', $employee->id) }}" class="edit-icon"><i class="fas fa-pencil-alt"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card tab-box">
            <div class="row user-tabs">
                <div class="col-lg-12 col-md-12 col-sm-12 line-tabs">
                    <ul class="nav nav-tabs nav-tabs-bottom">
                        <li class="nav-item"><a href="#emp_profile" data-bs-toggle="tab" class="nav-link active">الملف الشخصي</a></li>
                        <li class="nav-item"><a href="#emp_documents" data-bs-toggle="tab" class="nav-link">المستندات</a></li>
                        <li class="nav-item"><a href="#emp_shifts" data-bs-toggle="tab" class="nav-link">المناوبات</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="tab-content">
            <!-- Profile Info Tab -->
            <div id="emp_profile" class="pro-overview tab-pane fade show active">
                <div class="row">
                    <div class="col-md-6 d-flex">
                        <div class="card profile-box flex-fill">
                            <div class="card-body">
                                <h3 class="card-title">المعلومات الشخصية</h3>
                                <ul class="personal-info">
                                    <li><div class="title">المؤهل العلمي</div><div class="text">{{ $employee->qualification ?? '-' }}</div></li>
                                    <li><div class="title">الحالة الاجتماعية</div><div class="text">{{ $employee->marital_status ?? '-' }}</div></li>
                                    @if($employee->user)
                                    <li><div class="title">حساب المستخدم</div><div class="text">{{ $employee->user->name }}</div></li>
                                    <li><div class="title">دور المستخدم</div><div class="text">{{ $employee->user->role->name }}</div></li>
                                    <li><div class="title">حالة الحساب</div><div class="text">{{ $employee->user->is_active ? 'نشط' : 'غير نشط' }}</div></li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                    {{-- You can add more info boxes here --}}
                </div>
            </div>
            <!-- /Profile Info Tab -->

            <!-- Documents Tab -->
            <div class="tab-pane fade" id="emp_documents">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">المستندات المرفقة
                            <a href="{{ route('hr.documents.create', $employee->id) }}" class="btn btn-primary btn-sm float-end"><i class="fa fa-plus"></i> إضافة مستند</a>
                        </h3>
                        <div class="table-responsive">
                            <table class="table table-striped custom-table">
                                <thead>
                                    <tr>
                                        <th>نوع المستند</th>
                                        <th>اسم الملف</th>
                                        <th>تاريخ الرفع</th>
                                        <th class="text-end">الإجراء</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($employee->documents as $doc)
                                    <tr>
                                        <td>{{ $doc->document_type }}</td>
                                        <td>{{ $doc->file_name }}</td>
                                        <td>{{ $doc->uploaded_at->format('Y-m-d') }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('hr.documents.download', [$employee->id, $doc->id]) }}" class="btn btn-sm btn-outline-success"><i class="fa fa-download"></i></a>
                                            <form action="{{ route('hr.documents.destroy', [$employee->id, $doc->id]) }}" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد؟')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="4" class="text-center">لا توجد مستندات مرفقة.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                             <a href="{{ route('hr.documents.index', $employee->id) }}" class="btn btn-outline-primary">إدارة كل المستندات</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Documents Tab -->

            <!-- Shifts Tab -->
            <div class="tab-pane fade" id="emp_shifts">
                 <div class="card">
                     <div class="card-body">
                          <h3 class="card-title">مناوبات الموظف القادمة</h3>
                          {{-- Here you could load upcoming shifts via AJAX or just show a link --}}
                          <p>لإدارة وعرض مناوبات هذا الموظف، يرجى استخدام تقويم المناوبات.</p>
                          <a href="{{ route('hr.employee_shifts.calendar') }}?employee_id={{ $employee->id }}" class="btn btn-primary">عرض المناوبات في التقويم</a>
                     </div>
                 </div>
            </div>
            <!-- /Shifts Tab -->
        </div>
    </div>
</div>
@endsection