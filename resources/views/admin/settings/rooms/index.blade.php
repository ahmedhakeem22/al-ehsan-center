@php $page = 'admin-rooms-list'; @endphp
@extends('layout.mainlayout')
@section('title', 'قائمة الغرف')

@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            @component('components.page-header')
                @slot('title')
                    إدارة الغرف
                @endslot
                @slot('li_1')
                    <a href="{{ route('admin.settings.index') }}">الإعدادات</a>
                @endslot
                @slot('li_2')
                    الغرف
                @endslot
            @endcomponent

            <div class="row mb-3">
                <div class="col-md-12 text-end">
                    <a href="{{ route('admin.settings.rooms.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> إضافة غرفة جديدة
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card card-table">
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif
                            <div class="table-responsive">
                                <table class="table table-hover table-center mb-0 datatable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>الطابق</th>
                                            <th>رقم/اسم الغرفة</th>
                                            <th>السعة</th>
                                            <th>عدد الأسرة الحالية</th>
                                            <th class="text-end">إجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($rooms as $room)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $room->floor->name }}</td>
                                                <td>{{ $room->room_number }}</td>
                                                <td>{{ $room->capacity }}</td>
                                                <td>{{ $room->beds_count ?? $room->beds()->count() }}</td>
                                                <td class="text-end">
                                                    <a href="{{ route('admin.settings.rooms.edit', $room->id) }}" class="btn btn-sm btn-outline-warning me-1">
                                                        <i class="fas fa-edit"></i> تعديل
                                                    </a>
                                                    <form action="{{ route('admin.settings.rooms.destroy', $room->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذه الغرفة وجميع الأسرة التابعة لها (إذا كانت غير مشغولة)؟')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-trash"></i> حذف
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">لا توجد غرف مضافة حاليًا.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                {{ $rooms->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection