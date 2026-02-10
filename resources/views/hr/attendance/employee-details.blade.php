@php $page = 'hr-attendance-details'; @endphp
@extends('layout.mainlayout')
@section('title', 'سجل حضور الموظف - ' . $employee->full_name)

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        @component('components.page-header')
            @slot('title')
                سجل حضور: {{ $employee->full_name }}
            @endslot
            @slot('li_1')
                <a href="{{ route('hr.attendance.index') }}">قائمة الحضور</a>
            @endslot
            @slot('li_2')
                سجل الموظف
            @endslot
        @endcomponent

        {{-- Employee Info Card --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2 text-center">
                        <img class="rounded-circle img-fluid" src="{{ $employee->profile_picture_path ? Storage::url($employee->profile_picture_path) : asset('assets/img/profiles/avatar-01.jpg') }}" alt="User Image">
                    </div>
                    <div class="col-md-10">
                        <h4>{{ $employee->full_name }}</h4>
                        <p class="text-muted mb-1">{{ $employee->job_title }}</p>
                        <p class="text-muted mb-0">{{ $employee->user->email ?? 'لا يوجد بريد إلكتروني' }} | {{ $employee->phone_number ?? 'لا يوجد رقم هاتف' }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Attendance Records Table --}}
        <div class="row">
            <div class="col-sm-12">
                <div class="card card-table">
                    <div class="card-header">
                        <h4 class="card-title">سجل الحضور والانصراف</h4>
                        {{--  يمكن إضافة فلتر حسب الشهر والسنة هنا لاحقاً --}}
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-center mb-0">
                                <thead>
                                    <tr>
                                        <th>التاريخ</th>
                                        <th>وقت الحضور</th>
                                        <th>وقت الانصراف</th>
                                        <th>مدة العمل</th>
                                        <th>ملاحظات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($records as $record)
                                        <tr>
                                            <td>{{ $record->attendance_date->translatedFormat('l, d F Y') }}</td>
                                            <td>
                                                @if ($record->check_in_time)
                                                    <span class="text-success">{{ $record->check_in_time->format('h:i:s A') }}</span>
                                                @else
                                                    <span class="badge bg-danger-light">غائب</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($record->check_out_time)
                                                    <span class="text-danger">{{ $record->check_out_time->format('h:i:s A') }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($record->check_in_time && $record->check_out_time)
                                                    {{ $record->check_out_time->diffForHumans($record->check_in_time, true) }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{-- يمكن إضافة حقل للملاحظات هنا في المستقبل --}}
                                                -
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">لا توجد سجلات حضور لهذا الموظف.</td>
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