@php $page = 'admin-floors-list'; @endphp
@extends('layout.mainlayout')
@section('title', 'قائمة الطوابق')

@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            @component('components.page-header')
                @slot('title')
                    إدارة الطوابق
                @endslot
                @slot('li_1')
                    <a href="{{ route('admin.settings.index') }}">الإعدادات</a>
                @endslot
                @slot('li_2')
                    الطوابق
                @endslot
            @endcomponent

            <div class="row mb-3">
                <div class="col-md-12 text-end">
                    <a href="{{ route('admin.settings.floors.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> إضافة طابق جديد
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
                                            <th>اسم الطابق</th>
                                            <th>الوصف</th>
                                            <th>عدد الغرف</th>
                                            <th class="text-end">إجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($floors as $floor)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $floor->name }}</td>
                                                <td>{{ Str::limit($floor->description, 50) ?: '-' }}</td>
                                                <td>{{ $floor->rooms_count ?? $floor->rooms()->count() }}</td> {{-- افترض أنك قد تضيف rooms_count مع withCount --}}
                                                <td class="text-end">
                                                    <a href="{{ route('admin.settings.floors.edit', $floor->id) }}" class="btn btn-sm btn-outline-warning me-1">
                                                        <i class="fas fa-edit"></i> تعديل
                                                    </a>
                                                    <form action="{{ route('admin.settings.floors.destroy', $floor->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذا الطابق؟')">
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
                                                <td colspan="5" class="text-center">لا توجد طوابق مضافة حاليًا.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                             <div class="mt-3">
                                {{ $floors->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection