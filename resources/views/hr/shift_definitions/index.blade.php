@php $page = 'hr-shift-def-list'; @endphp
@extends('layout.mainlayout')
@section('title', 'تعريفات المناوبات')

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        @component('components.page-header')
            @slot('title')
                تعريفات المناوبات
            @endslot
            @slot('li_1')
                الموارد البشرية
            @endslot
            @slot('li_2')
                تعريفات المناوبات
            @endslot
        @endcomponent

        <div class="row mb-3">
            <div class="col-md-12 text-end">
                <a href="{{ route('hr.shift_definitions.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> إضافة مناوبة جديدة
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
                            <table class="table table-hover table-center mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>اسم المناوبة</th>
                                        <th>وقت البدء</th>
                                        <th>وقت الانتهاء</th>
                                        <th>المدة (ساعة)</th>
                                        <th class="text-end">إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($shiftDefinitions as $def)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <i class="fas fa-circle me-2" style="color:{{$def->color_code}}"></i> {{$def->name}}
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($def->start_time)->format('h:i A') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($def->end_time)->format('h:i A') }}</td>
                                        <td>{{ $def->duration_hours }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('hr.shift_definitions.edit', $def->id) }}" class="btn btn-sm btn-outline-warning me-1">
                                                <i class="fas fa-edit"></i> تعديل
                                            </a>
                                            <form action="{{ route('hr.shift_definitions.destroy', $def->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذا التعريف؟ لا يمكنك الحذف إذا كان مستخدماً في جدول المناوبات.')">
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
                                        <td colspan="6" class="text-center">لا توجد تعريفات للمناوبات حالياً.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                         <div class="mt-3">
                            {{ $shiftDefinitions->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection