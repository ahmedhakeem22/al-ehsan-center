@php $page = 'hr-employees-list'; @endphp
@extends('layout.mainlayout')
@section('title', 'قائمة الموظفين')

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
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

        <div class="row mb-3">
            <div class="col-md-12 text-end">
                <a href="{{ route('hr.employees.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> إضافة موظف جديد
                </a>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('hr.employees.index') }}">
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <div class="form-group local-forms">
                                <label>بحث (بالاسم، الهاتف، المسمى)</label>
                                <input type="text" name="search" class="form-control" value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group local-forms">
                                <label>المسمى الوظيفي</label>
                                <select name="job_title" class="form-control select">
                                    <option value="">الكل</option>
                                    @foreach($jobTitles as $title)
                                    <option value="{{ $title }}" @selected(request('job_title') == $title)>{{ $title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary">بحث</button>
                            <a href="{{ route('hr.employees.index') }}" class="btn btn-secondary">إعادة تعيين</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>


        <div class="row">
            <div class="col-sm-12">
                <div class="card card-table">
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
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <h2 class="table-avatar">
                                                <a href="{{ route('hr.employees.show', $employee->id) }}" class="avatar avatar-sm me-2">
                                                    <img class="avatar-img rounded-circle" src="{{ $employee->profile_picture_path ? Storage::url($employee->profile_picture_path) : asset('assets/img/profiles/avatar-01.jpg') }}" alt="User Image">
                                                </a>
                                                <a href="{{ route('hr.employees.show', $employee->id) }}">{{ $employee->full_name }}</a>
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
                                            <a href="{{ route('hr.employees.show', $employee->id) }}" class="btn btn-sm btn-outline-info me-1"><i class="fas fa-eye"></i></a>
                                            <a href="{{ route('hr.employees.edit', $employee->id) }}" class="btn btn-sm btn-outline-warning me-1"><i class="fas fa-edit"></i></a>
                                            <form action="{{ route('hr.employees.destroy', $employee->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذا الموظف وجميع بياناته المرتبطة؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">لا يوجد موظفون حالياً.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $employees->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection