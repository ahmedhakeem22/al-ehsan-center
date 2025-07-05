@php $page = 'hr-attendance-report'; @endphp
@extends('layout.mainlayout')
@section('title', 'تقرير الحضور الشامل')

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        @component('components.page-header')
            @slot('title')
                تقرير الحضور الشامل
            @endslot
            @slot('li_1')
                الموارد البشرية
            @endslot
            @slot('li_2')
                تقارير الحضور
            @endslot
        @endcomponent

        <!-- Search Filter -->
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('hr.attendance-reports.index') }}">
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <div class="form-group local-forms">
                                <label>الموظف</label>
                                <select name="employee_id" class="form-control select">
                                    <option value="">جميع الموظفين</option>
                                    @foreach($employees as $id => $name)
                                        <option value="{{ $id }}" @selected(request('employee_id') == $id)>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group local-forms">
                                <label>حالة الحضور</label>
                                <select name="status" class="form-control select">
                                    <option value="">الكل</option>
                                    <option value="present_on_time" @selected(request('status') == 'present_on_time')>حاضر (في الوقت)</option>
                                    <option value="late" @selected(request('status') == 'late')>متأخر</option>
                                    <option value="absent" @selected(request('status') == 'absent')>غائب</option>
                                    <option value="unscheduled" @selected(request('status') == 'unscheduled')>حضور غير مجدول</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group local-forms">
                                <label>من تاريخ</label>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from', today()->subMonth()->toDateString()) }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group local-forms">
                                <label>إلى تاريخ</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to', today()->toDateString()) }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary">بحث</button>
                            <a href="{{ route('hr.attendance-reports.index') }}" class="btn btn-secondary mt-2">إعادة تعيين</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card card-table">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-center mb-0">
                                <thead>
                                    <tr>
                                        <th>التاريخ</th>
                                        <th>الموظف</th>
                                        <th>المناوبة</th>
                                        <th>وقت الدخول</th>
                                        <th>وقت الخروج</th>
                                        <th>ساعات العمل</th>
                                        <th>الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($reportData as $record)
                                    <tr>
                                        <td>{{ $record['date']->format('Y-m-d') }}</td>
                                        <td>
                                            <a href="{{ route('hr.employees.attendance-report', $record['employee']->id) }}">{{ $record['employee']->full_name }}</a>
                                        </td>
                                        <td>{{ $record['shift_definition']?->name ?? 'غير مجدول' }}</td>
                                        <td>{{ $record['attendance']?->check_in_time?->format('h:i:s A') ?? '-' }}</td>
                                        <td>{{ $record['attendance']?->check_out_time?->format('h:i:s A') ?? '-' }}</td>
                                        <td>{{ $record['attendance']?->formatted_worked_hours ?? '-' }}</td>
                                        <td>
                                            @if($record['status'] == 'present')
                                                <span class="badge bg-success-light">حاضر</span>
                                            @elseif($record['status'] == 'late')
                                                <span class="badge bg-warning-light">متأخر</span>
                                            @elseif($record['status'] == 'absent')
                                                <span class="badge bg-danger-light">غائب</span>
                                            @elseif($record['status'] == 'scheduled')
                                                <span class="badge bg-info-light">مجدول</span>
                                            @elseif($record['status'] == 'unscheduled')
                                                <span class="badge bg-primary-light">حضور غير مجدول</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center">لا توجد سجلات تطابق البحث.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $reportData->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection