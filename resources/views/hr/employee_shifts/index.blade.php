@php $page = 'hr-shifts-list'; @endphp
@extends('layout.mainlayout')
@section('title', 'قائمة المناوبات')

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        @component('components.page-header')
            @slot('title')
                جدول المناوبات
            @endslot
            @slot('li_1')
                الموارد البشرية
            @endslot
            @slot('li_2')
                قائمة المناوبات
            @endslot
        @endcomponent

        <div class="row mb-3">
            <div class="col-md-12 text-end">
                <a href="{{ route('hr.employee_shifts.calendar') }}" class="btn btn-info">
                    <i class="fas fa-calendar-alt"></i> عرض التقويم
                </a>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('hr.employee_shifts.index') }}">
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <div class="form-group local-forms">
                                <label>الموظف</label>
                                <select name="employee_id" class="form-control select">
                                    <option value="">كل الموظفين</option>
                                    @foreach($employees as $id => $name)
                                        <option value="{{ $id }}" @selected(request('employee_id') == $id)>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group local-forms">
                                <label>نوع المناوبة</label>
                                <select name="shift_definition_id" class="form-control select">
                                    <option value="">كل المناوبات</option>
                                    @foreach($shiftDefinitions as $id => $name)
                                        <option value="{{ $id }}" @selected(request('shift_definition_id') == $id)>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group local-forms">
                                <label>من تاريخ</label>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group local-forms">
                                <label>إلى تاريخ</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary">بحث</button>
                            <a href="{{ route('hr.employee_shifts.index') }}" class="btn btn-secondary">إعادة تعيين</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>


        <div class="row">
            <div class="col-sm-12">
                <div class="card card-table">
                    <div class="card-body">
                         @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        <div class="table-responsive">
                            <table class="table table-hover table-center mb-0">
                                <thead>
                                    <tr>
                                        <th>الموظف</th>
                                        <th>تاريخ المناوبة</th>
                                        <th>نوع المناوبة</th>
                                        <th>الوقت</th>
                                        <th>عُيّنت بواسطة</th>
                                        <th class="text-end">إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($employeeShifts as $shift)
                                    <tr>
                                        <td>{{ $shift->employee->full_name }}</td>
                                        <td>{{ $shift->shift_date->format('Y-m-d') }}</td>
                                        <td>
                                            <span class="badge" style="background-color:{{ $shift->definition->color_code ?? '#777' }}; color: #fff;">
                                                {{ $shift->definition->name }}
                                            </span>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($shift->definition->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($shift->definition->end_time)->format('h:i A') }}</td>
                                        <td>{{ $shift->assigner->name ?? '-' }}</td>
                                        <td class="text-end">
                                            {{-- Edit/Delete actions can be handled via modals in the calendar view --}}
                                            <form action="{{ route('hr.employee_shifts.destroy', $shift->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذه المناوبة؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i> حذف</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">لا توجد مناوبات تطابق البحث.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $employeeShifts->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection