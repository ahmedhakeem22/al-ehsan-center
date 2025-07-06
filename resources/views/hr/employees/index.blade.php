@php $page = 'hr-employees-list'; @endphp
@extends('layout.mainlayout')
@section('title', 'قائمة الموظفين')

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        {{-- 1. رأس الصفحة --}}
        @component('components.page-header')
            @slot('title')
                الموظفين
            @endslot
            @slot('li_1')
                الموارد البشرية
            @endslot
            @slot('li_2')
                قائمة الموظفين
            @endslot
        @endcomponent

        {{-- 2. زر الإجراء الرئيسي --}}
        <div class="row mb-4">
            <div class="col-md-12 text-end">
                <a href="{{ route('hr.employees.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> إضافة موظف جديد
                </a>
            </div>
        </div>
        
        {{-- 3. بطاقة الفلترة والبحث --}}
        <div class="card">
            <div class="card-header">
                <h5 class="card-title"><i class="fas fa-filter me-2"></i>فلترة البحث</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('hr.employees.index') }}">
                    <div class="row align-items-end">
                        <div class="col-md-5">
                            <label for="search" class="form-label">بحث</label>
                            <input type="text" id="search" name="search" class="form-control" placeholder="بحث بالاسم، الهاتف، المسمى..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="job_title" class="form-label">المسمى الوظيفي</label>
                            <select name="job_title" id="job_title" class="form-select">
                                <option value="">الكل</option>
                                @foreach($jobTitles as $title)
                                <option value="{{ $title }}" @selected(request('job_title') == $title)>{{ $title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 d-flex mt-4 mt-md-0">
                            <button type="submit" class="btn btn-primary flex-grow-1 me-2"><i class="fas fa-search"></i> بحث</button>
                            <a href="{{ route('hr.employees.index') }}" class="btn btn-outline-secondary flex-grow-1"><i class="fas fa-redo"></i> إعادة</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- 4. بطاقة عرض النتائج --}}
        <div class="row mt-4">
            <div class="col-sm-12">
                <div class="card card-table">
                    <div class="card-header">
                        {{-- تمت إضافة عدد النتائج هنا --}}
                        <h5 class="card-title">قائمة الموظفين ({{ $employees->total() }})</h5>
                    </div>
                    <div class="card-body">
                         @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        <div class="table-responsive">
                            <table class="table table-hover table-center mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>اسم الموظف</th>
                                        <th>المسمى الوظيفي</th>
                                        <th>رقم الهاتف</th>
                                        <th>حساب المستخدم</th>
                                        <th class="text-end">إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($employees as $employee)
                                    <tr>
                                        {{-- تم إصلاح الترقيم هنا --}}
                                        <td>{{ $employees->firstItem() + $loop->index }}</td>
                                        <td>
                                            <h2 class="table-avatar">
                                                <a href="{{ route('hr.employees.show', $employee->id) }}" class="avatar avatar-sm me-2">
                                                    <img class="avatar-img rounded-circle" src="{{ $employee->profile_picture_path ? Storage::url($employee->profile_picture_path) : asset('assets/img/profiles/avatar-01.jpg') }}" alt="User Image">
                                                </a>
                                                <a href="{{ route('hr.employees.show', $employee->id) }}" class="fw-bold">{{ $employee->full_name }}</a>
                                            </h2>
                                        </td>
                                        <td>{{ $employee->job_title }}</td>
                                        <td>{{ $employee->phone_number ?? '-' }}</td>
                                        <td>
                                            @if($employee->user)
                                                <span class="badge bg-success-light">{{ $employee->user->name }} ({{ $employee->user->role->name }})</span>
                                            @else
                                                <span class="badge bg-danger-light">غير مربوط</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('hr.employees.show', $employee->id) }}" class="btn btn-sm btn-outline-info me-1" title="عرض"><i class="fas fa-eye"></i></a>
                                            <a href="{{ route('hr.employees.edit', $employee->id) }}" class="btn btn-sm btn-outline-warning me-1" title="تعديل"><i class="fas fa-edit"></i></a>
                                            <form action="{{ route('hr.employees.destroy', $employee->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الموظف وجميع بياناته المرتبطة؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <h5>لا توجد نتائج تطابق بحثك</h5>
                                            <p>حاول تغيير فلترة البحث أو قم بإضافة موظف جديد.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- 5. كود الـ Pagination الصحيح --}}
                        @if ($employees->hasPages())
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $employees->appends(request()->query())->links('pagination::bootstrap-5') }}
                        </div>
                        @endif
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection