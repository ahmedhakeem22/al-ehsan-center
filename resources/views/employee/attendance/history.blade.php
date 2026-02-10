@php $page = 'employee-attendance-history'; @endphp
@extends('layout.mainlayout')
@section('title', 'سجل الحضور الخاص بي')

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        @component('components.page-header')
            @slot('title')
                سجل الحضور الخاص بي
            @endslot
            @slot('li_1')
                <a href="{{ route('employee.attendance.index') }}">الحضور والإنصراف</a>
            @endslot
            @slot('li_2')
                سجل الحضور
            @endslot
        @endcomponent

        <div class="row">
            <div class="col-sm-12">
                <div class="card card-table">
                    <div class="card-header">
                        <h4 class="card-title">سجل الحضور والانصراف</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-center mb-0">
                                <thead>
                                    <tr>
                                        <th>التاريخ</th>
                                        <th>وقت الحضور</th>
                                        <th>وقت الانصراف</th>
                                        <th>إجمالي مدة العمل</th>
                                        <th>الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($records as $record)
                                        <tr>
                                            <td>{{ $record->attendance_date->translatedFormat('l, d F Y') }}</td>
                                            <td>
                                                @if ($record->check_in_time)
                                                    <span class="text-success fw-bold">{{ $record->check_in_time->format('h:i:s A') }}</span>
                                                @else
                                                    {{-- هذه الحالة تعني غياب، وقد لا تظهر إذا كنت لا تنشئ سجلات للأيام الغائبة --}}
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($record->check_out_time)
                                                    <span class="text-danger fw-bold">{{ $record->check_out_time->format('h:i:s A') }}</span>
                                                @else
                                                    <span class="text-warning">لم يتم تسجيل الانصراف</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($record->check_in_time && $record->check_out_time)
                                                    {{-- حساب الفرق بين الوقتين --}}
                                                    {{ $record->check_in_time->diff($record->check_out_time)->format('%H ساعة و %i دقيقة') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if ($record->check_in_time && $record->check_out_time)
                                                    <span class="badge rounded-pill bg-success-light">مكتمل</span>
                                                @elseif ($record->check_in_time)
                                                    <span class="badge rounded-pill bg-warning-light">حاضر</span>
                                                @else
                                                    <span class="badge rounded-pill bg-danger-light">غائب</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">لا يوجد لديك سجلات حضور سابقة.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                         {{-- Pagination links --}}
                        <div class="mt-4">
                            {{ $records->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection