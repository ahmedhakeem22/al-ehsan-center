@php $page = 'hr-attendance-dashboard'; @endphp
@extends('layout.mainlayout')
@section('title', 'لوحة معلومات الحضور')

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        @component('components.page-header')
            @slot('title')
                لوحة معلومات الحضور
            @endslot
            @slot('li_1')
                الموارد البشرية
            @endslot
            @slot('li_2')
                الحضور والانصراف
            @endslot
        @endcomponent

        <!-- Date Filter -->
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('hr.attendance-reports.dashboard') }}">
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <div class="form-group local-forms">
                                <label for="date-filter">اختر التاريخ</label>
                                <input type="date" id="date-filter" name="date" class="form-control" value="{{ $date->toDateString() }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary">عرض التقرير</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- KPIs Cards -->
        <div class="row">
            <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                <div class="card dash-widget">
                    <div class="card-body">
                        <span class="dash-widget-icon"><i class="fas fa-calendar-alt"></i></span>
                        <div class="dash-widget-info">
                            <h3>{{ $stats['totalScheduled'] }}</h3>
                            <span>مناوبات مجدولة</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                <div class="card dash-widget">
                    <div class="card-body">
                        <span class="dash-widget-icon text-success"><i class="fas fa-user-check"></i></span>
                        <div class="dash-widget-info">
                            <h3>{{ $stats['totalPresent'] }}</h3>
                            <span>إجمالي الحاضرين</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                <div class="card dash-widget">
                    <div class="card-body">
                        <span class="dash-widget-icon text-danger"><i class="fas fa-user-times"></i></span>
                        <div class="dash-widget-info">
                            <h3>{{ $stats['absentCount'] }}</h3>
                            <span>الغائبون (من المجدولين)</span>
                        </div>
                    </div>
                </div>
            </div>
             <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                <div class="card dash-widget">
                    <div class="card-body">
                        <span class="dash-widget-icon text-warning"><i class="fas fa-clock"></i></span>
                        <div class="dash-widget-info">
                            <h3>{{ $stats['lateCount'] }}</h3>
                            <span>المتأخرون (من المجدولين)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Details Tables -->
        <div class="row">
            <div class="col-12">
                <div class="card card-table">
                    <div class="card-header">
                        <h4 class="card-title">حضور غير مجدول ({{ $stats['presentUnscheduled'] }})</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-center mb-0">
                                <thead>
                                    <tr>
                                        <th>الموظف</th>
                                        <th>وقت الحضور</th>
                                        <th>وقت الانصراف</th>
                                        <th>مدة العمل</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($unscheduledAttendance as $attendance)
                                    <tr>
                                        <td>{{ $attendance->employee->full_name }}</td>
                                        <td><span class="badge bg-primary-light">{{ $attendance->check_in_time->format('h:i A') }}</span></td>
                                        <td>{{ $attendance->check_out_time ? $attendance->check_out_time->format('h:i A') : 'لم يسجل انصراف' }}</td>
                                        <td>{{ $attendance->formatted_worked_hours }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center">لا يوجد حضور غير مجدول اليوم.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <!-- Late Arrivals -->
            <div class="col-md-6 d-flex">
                <div class="card card-table flex-fill">
                    <div class="card-header">
                        <h4 class="card-title">المتأخرون (من المجدولين)</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-center mb-0">
                                <thead>
                                    <tr>
                                        <th>الموظف</th>
                                        <th>وقت الحضور</th>
                                        <th>وقت المناوبة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($lateArrivals as $attendance)
                                    <tr>
                                        <td>{{ $attendance->employee->full_name }}</td>
                                        <td><span class="badge bg-warning">{{ $attendance->check_in_time->format('h:i A') }}</span></td>
                                        <td>{{ \Carbon\Carbon::parse($attendance->shift?->definition?->start_time)->format('h:i A') ?? 'N/A' }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center">لا يوجد متأخرون اليوم.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Absent Employees -->
            <div class="col-md-6 d-flex">
                <div class="card card-table flex-fill">
                     <div class="card-header">
                        <h4 class="card-title">الغائبون (من المجدولين)</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-center mb-0">
                                <thead>
                                    <tr>
                                        <th>الموظف</th>
                                        <th>المناوبة المجدولة</th>
                                        <th>الوقت</th>
                                    </tr>
                                </thead>
                                <tbody>
                                     @forelse ($absentEmployees as $shift)
                                    <tr>
                                        <td>{{ $shift->employee->full_name }}</td>
                                        <td>{{ $shift->definition->name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($shift->definition->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($shift->definition->end_time)->format('h:i A') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center">لا يوجد غائبون اليوم.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection