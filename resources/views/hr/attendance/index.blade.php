@php $page = 'hr-attendance-list'; @endphp
@extends('layout.mainlayout')
@section('title', 'مراقبة حضور الموظفين')

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        @component('components.page-header')
            @slot('title')
                سجلات الحضور
            @endslot
            @slot('li_1')
                لوحة التحكم
            @endslot
            @slot('li_2')
                قائمة الحضور
            @endslot
        @endcomponent

        {{-- Filter Card --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">تصفية السجلات</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('hr.attendance.index') }}" method="GET">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="employee_id">اختر الموظف</label>
                                <select name="employee_id" id="employee_id" class="form-control select2">
                                    <option value="">جميع الموظفين</option>
                                    @foreach ($employees as $id => $name)
                                        <option value="{{ $id }}" {{ request('employee_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="date">اختر التاريخ</label>
                                <input type="date" name="date" id="date" class="form-control" value="{{ request('date', \Carbon\Carbon::today()->format('Y-m-d')) }}">
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-group w-100">
                                <button type="submit" class="btn btn-primary w-50">بحث</button>
                                <a href="{{ route('hr.attendance.index') }}" class="btn btn-secondary w-45">إعادة تعيين</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Attendance Table --}}
        <div class="row">
            <div class="col-sm-12">
                <div class="card card-table">
                    <div class="card-header">
                        <h4 class="card-title">سجلات الحضور لتاريخ: {{ \Carbon\Carbon::parse(request('date', now()))->translatedFormat('d F Y') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-center mb-0">
                                <thead>
                                    <tr>
                                        <th>الموظف</th>
                                        <th>وقت الحضور</th>
                                        <th>وقت الانصراف</th>
                                        <th>مدة العمل</th>
                                        <th>حالة الحضور</th>
                                        <th class="text-end">إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($records as $record)
                                        <tr>
                                            <td>
                                                <h2 class="table-avatar">
                                                    <a href="{{ route('hr.attendance.employee.details', $record->employee->id) }}" class="avatar avatar-sm me-2">
                                                        <img class="avatar-img rounded-circle" src="{{ $record->employee->profile_picture_path ? Storage::url($record->employee->profile_picture_path) : asset('assets/img/profiles/avatar-01.jpg') }}" alt="User Image">
                                                    </a>
                                                    <a href="{{ route('hr.attendance.employee.details', $record->employee->id) }}">{{ $record->employee->full_name }}</a>
                                                </h2>
                                            </td>
                                            <td>
                                                @if ($record->check_in_time)
                                                    <span class="text-success fw-bold">{{ $record->check_in_time->format('h:i:s A') }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($record->check_out_time)
                                                    <span class="text-danger fw-bold">{{ $record->check_out_time->format('h:i:s A') }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($record->check_in_time && $record->check_out_time)
                                                    {{ $record->check_out_time->diff($record->check_in_time)->format('%H ساعة و %i دقيقة') }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($record->check_in_time && $record->check_out_time)
                                                    <span class="badge bg-success-light">مكتمل</span>
                                                @elseif ($record->check_in_time)
                                                    <span class="badge bg-warning-light">حاضر</span>
                                                @else
                                                     {{-- هذه الحالة لا يجب أن تظهر في الظروف العادية --}}
                                                    <span class="badge bg-danger-light">غائب</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('hr.attendance.employee.details', $record->employee->id) }}" class="btn btn-sm btn-outline-info">
                                                    عرض السجل الكامل
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">لا توجد سجلات حضور تطابق معايير البحث.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $records->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "اختر موظف",
            allowClear: true
        });
    });
</script>
@endpush