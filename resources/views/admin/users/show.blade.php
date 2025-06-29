@php $page = 'admin-users-show'; @endphp
@extends('layout.mainlayout')
@section('title', 'ملف المستخدم - ' . $user->name)

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        @component('components.page-header')
            @slot('title')
                ملف المستخدم
            @endslot
            @slot('li_1')
                 <a href="{{ route('admin.users.index') }}">المستخدمين</a>
            @endslot
            @slot('li_2')
                عرض التفاصيل
            @endslot
        @endcomponent

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="profile-view">
                            <div class="profile-basic">
                                <div class="row">
                                    <div class="col-md-7">
                                        <ul class="personal-info">
                                            <li><div class="title">الاسم:</div><div class="text">{{ $user->name }}</div></li>
                                            <li><div class="title">اسم المستخدم:</div><div class="text">{{ $user->username }}</div></li>
                                            <li><div class="title">البريد الإلكتروني:</div><div class="text"><a href="mailto:{{ $user->email }}">{{ $user->email }}</a></div></li>
                                            <li><div class="title">الدور:</div><div class="text">{{ $user->role->name }}</div></li>
                                            <li><div class="title">الحالة:</div><div class="text">{!! $user->is_active ? '<span class="badge bg-success">نشط</span>' : '<span class="badge bg-danger">غير نشط</span>' !!}</div></li>
                                            <li><div class="title">تاريخ الإنشاء:</div><div class="text">{{ $user->created_at->format('d M Y') }}</div></li>
                                        </ul>
                                    </div>
                                    <div class="col-md-5">
                                        {{-- Additional Info if needed --}}
                                        @if($user->employeeRecord)
                                            <h5>الحساب مرتبط بالموظف:</h5>
                                            <p><a href="{{ route('hr.employees.show', $user->employeeRecord->id) }}">{{ $user->employeeRecord->full_name }}</a></p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="pro-edit">
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="edit-icon"><i class="fas fa-pencil-alt"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection