@php $page = 'admin-users-list'; @endphp
@extends('layout.mainlayout')
@section('title', 'إدارة المستخدمين')

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        @component('components.page-header')
            @slot('title')
                المستخدمون
            @endslot
            @slot('li_1')
                الإدارة
            @endslot
            @slot('li_2')
                قائمة المستخدمين
            @endslot
        @endcomponent

        <div class="row mb-3">
            <div class="col-md-12 text-end">
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> إضافة مستخدم جديد
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card card-table">
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success" role="alert">{{ session('success') }}</div>
                        @endif
                        @if (session('error'))
                             <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
                        @endif
                        <div class="table-responsive">
                            <table class="table table-hover table-center mb-0 datatable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>الاسم</th>
                                        <th>اسم المستخدم</th>
                                        <th>البريد الإلكتروني</th>
                                        <th>الدور</th>
                                        <th>الحالة</th>
                                        <th class="text-end">إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td><a href="{{ route('admin.users.show', $user->id) }}">{{ $user->name }}</a></td>
                                            <td>{{ $user->username }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->role->name ?? 'غير محدد' }}</td>
                                            <td>
                                                @if($user->is_active)
                                                    <span class="badge bg-success-light">نشط</span>
                                                @else
                                                    <span class="badge bg-danger-light">غير نشط</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-outline-info me-1"><i class="fas fa-eye"></i></a>
                                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-outline-warning me-1"><i class="fas fa-edit"></i></a>
                                                @if(auth()->id() !== $user->id)
                                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                                </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                         <div class="mt-3">
                            {{ $users->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection