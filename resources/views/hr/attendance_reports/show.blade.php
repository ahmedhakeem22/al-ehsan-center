@php $page = 'hr-employee-report'; @endphp
@extends('layout.mainlayout')
@section('title', 'تقرير حضور الموظف')

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        @component('components.page-header')
            @slot('title')
                تقرير الحضور: {{ $employee->full_name }}
            @endslot
            @slot('li_1')
                <a href="{{ route('hr.employees.index') }}">الموظفين</a>
            @endslot
            @slot('li_2')
                تقرير الحضور
            @endslot
        @endcomponent

        <div class="card mb-0">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="profile-view">
                            <div class="profile-img-wrap">
                                <div class="profile-img">
                                    <a href="#"><img src="{{ $employee->profile_picture_path ? Storage::url($employee->profile_picture_path) : asset('assets/img/profiles/avatar-02.jpg') }}" alt="{{ $employee->full_name }}"></a>
                                </div>
                            </div>
                            <div class="profile-basic">
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="profile-info-left">
                                            <h3 class="user-name m-t-0 mb-0">{{ $employee->full_name }}</h3>
                                            <h6 class="text-muted">{{ $employee->job_title }}</h6>
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        <ul class="personal-info">
                                            <li><div class="title">الهاتف:</div><div class="text"><a href="">{{ $employee->phone_number ?? '-' }}</a></div></li>
                                            <li><div class="title">البريد الإلكتروني:</div><div class="text"><a href="">{{ $employee->user?->email ?? '-' }}</a></div></li>
                                            <li><div class="title">تاريخ الالتحاق:</div><div class="text">{{ $employee->joining_date?->format('d/m/Y') ?? '-' }}</div></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Filter and KPIs -->
        <div class="card mt-3">
            <div class="card-body">
                <form method="GET" action="{{ route('hr.employees.attendance-report', $employee->id) }}">
                    <div class="row align-items-end mb-4">
                        <div class="col-md-4">
                            <div class="form-group local-forms">
                                <label>من تاريخ</label>
                                <input type="date" name="date_from" class="form-control" value="{{ $dateFrom->toDateString() }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group local-forms">
                                <label>إلى تاريخ</label>
                                <input type="date" name="date_to" class="form-control" value="{{ $dateTo->toDateString() }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary">تصفية</button>
                        </div>
                    </div>
                </form>

                <h5 class="card-title">إحصائيات خلال الفترة المحددة</h5>
                <div class="row">
                    <div class="col-md-6 col-lg-3">
                        <div class="stats-info"><h6>أيام مجدولة</h6><h4>{{ $stats['scheduled_days'] }}</h4></div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="stats-info"><h6>حضور غير مجدول</h6><h4>{{ $stats['unscheduled_presence'] }}</h4></div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="stats-info text-success"><h6>إجمالي أيام الحضور</h6><h4>{{ $stats['present_days'] }}</h4></div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="stats-info text-danger"><h6>أيام الغياب (من المجدول)</h6><h4>{{ $stats['absent_days'] }}</h4></div>
                    </div>
                </div>
                 <div class="row mt-3">
                    <div class="col-md-6 col-lg-3">
                        <div class="stats-info text-warning"><h6>التأخير (من المجدول)</h6><h4>{{ $stats['late_arrivals'] }}</h4></div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="stats-info"><h6>نسبة الحضور (للمجدول)</h6><h4>{{ $stats['attendance_percentage'] }}%</h4></div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="stats-info"><h6>إجمالي ساعات العمل</h6><h4>{{ round($stats['total_worked_hours'], 2) }} ساعة</h4></div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="stats-info"><h6>متوسط ساعات العمل اليومي</h6><h4>{{ $stats['average_worked_hours'] }} ساعة</h4></div>
                    </div>
                 </div>
            </div>
        </div>

        <!-- Attendance Log Table -->
         <div class="row">
            <div class="col-sm-12">
                <div class="card card-table">
                    <div class="card-header"><h4 class="card-title">سجل الحضور</h4></div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-center mb-0">
                                <thead>
                                    <tr>
                                        <th>التاريخ</th>
                                        <th>المناوبة</th>
                                        <th>وقت الدخول</th>
                                        <th>وقت الخروج</th>
                                        <th>ساعات العمل</th>
                                        <th>الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($paginatedData as $record)
                                    <tr>
                                        <td>{{ $record['date']->format('Y-m-d') }}</td>
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
                                        <td colspan="6" class="text-center">لا توجد سجلات لهذا الموظف في الفترة المحددة.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $paginatedData->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection