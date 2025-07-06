@extends('layout.mainlayout')
@section('title', 'صرف الوصفات الطبية')

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">

        {{-- 1. رأس الصفحة --}}
        @component('components.page-header')
            @slot('title')
                الصيدلية
            @endslot
            @slot('li_1')
                وصفات قيد الصرف
            @endslot
        @endcomponent

        {{-- 2. بطاقة الفلترة والبحث --}}
        <div class="card">
            <div class="card-header">
                <h5 class="card-title"><i class="fas fa-filter me-2"></i> فلترة الوصفات</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('pharmacy.dispense.index') }}" method="GET">
                    <div class="row align-items-end">
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="patient_name" class="form-label">اسم المريض أو رقم الملف</label>
                            <input type="text" id="patient_name" name="patient_name" class="form-control" value="{{ request('patient_name') }}">
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="doctor_name" class="form-label">اسم الطبيب</label>
                            <input type="text" id="doctor_name" name="doctor_name" class="form-control" value="{{ request('doctor_name') }}">
                        </div>
                        <div class="col-md-2 mb-3 mb-md-0">
                            <label for="date_from" class="form-label">من تاريخ</label>
                            <input type="date" id="date_from" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2 mb-3 mb-md-0">
                             <label for="date_to" class="form-label">إلى تاريخ</label>
                            <input type="date" id="date_to" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-2 d-flex">
                            <button type="submit" class="btn btn-primary flex-grow-1 me-2"><i class="fas fa-search"></i> بحث</button>
                            <a href="{{ route('pharmacy.dispense.index') }}" class="btn btn-outline-secondary flex-grow-1"><i class="fas fa-redo"></i></a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- 3. بطاقة عرض النتائج --}}
        <div class="row mt-4">
            <div class="col-sm-12">
                <div class="card card-table">
                    <div class="card-header">
                        <h5 class="card-title">الوصفات الطبية الجاهزة للصرف ({{ $pendingPrescriptions->total() }})</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-center mb-0">
                                <thead>
                                    <tr>
                                        <th>رقم الوصفة</th>
                                        <th>اسم المريض</th>
                                        <th>رقم الملف</th>
                                        <th>الطبيب المعالج</th>
                                        <th>تاريخ الوصفة</th>
                                        <th class="text-center">الحالة</th>
                                        <th class="text-end">الإجراء</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($pendingPrescriptions as $prescription)
                                    <tr>
                                        <td><span class="badge bg-light text-dark">#{{ $prescription->id }}</span></td>
                                        <td>
                                            <a href="{{-- route('patient_management.patients.show', $prescription->patient_id) --}}" class="text-dark fw-bold">{{ $prescription->patient->full_name }}</a>
                                        </td>
                                        <td>{{ $prescription->patient->file_number }}</td>
                                        <td>{{ $prescription->doctor->name }}</td>
                                        <td>{{ $prescription->prescription_date->format('Y-m-d') }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-warning-light">قيد الصرف</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('pharmacy.dispense.show', $prescription->id) }}" class="btn btn-sm btn-success">
                                                <i class="fas fa-pills me-1"></i> بدء عملية الصرف
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <h5>لا توجد وصفات طبية قيد الصرف حالياً</h5>
                                            <p>سيتم عرض الوصفات هنا بمجرد أن يقوم الأطباء بإصدارها.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- 4. أزرار التنقل --}}
                        @if ($pendingPrescriptions->hasPages())
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $pendingPrescriptions->appends(request()->query())->links('pagination::bootstrap-5') }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection