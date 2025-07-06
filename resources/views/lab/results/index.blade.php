@extends('layout.mainlayout')
@section('title', 'إدخال نتائج المختبر')

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">

        @component('components.page-header')
            @slot('title') المختبر @endslot
            @slot('li_1') طلبات تنتظر النتائج @endslot
        @endcomponent

        <div class="card">
            <div class="card-header"><h5 class="card-title"><i class="fas fa-filter me-2"></i> فلترة الطلبات</h5></div>
            <div class="card-body">
                <form action="{{ route('lab.results.index') }}" method="GET">
                    <div class="row align-items-end">
                        <div class="col-md-3 mb-3 mb-md-0"><label for="patient_name" class="form-label">اسم المريض/رقم الملف</label><input type="text" id="patient_name" name="patient_name" class="form-control" value="{{ request('patient_name') }}"></div>
                        <div class="col-md-3 mb-3 mb-md-0"><label for="doctor_name" class="form-label">اسم الطبيب</label><input type="text" id="doctor_name" name="doctor_name" class="form-control" value="{{ request('doctor_name') }}"></div>
                        <div class="col-md-2 mb-3 mb-md-0"><label for="request_date_from" class="form-label">من تاريخ</label><input type="date" id="request_date_from" name="request_date_from" class="form-control" value="{{ request('request_date_from') }}"></div>
                        <div class="col-md-2 mb-3 mb-md-0"><label for="request_date_to" class="form-label">إلى تاريخ</label><input type="date" id="request_date_to" name="request_date_to" class="form-control" value="{{ request('request_date_to') }}"></div>
                        <div class="col-md-2 d-flex"><button type="submit" class="btn btn-primary flex-grow-1 me-2"><i class="fas fa-search"></i></button><a href="{{ route('lab.results.index') }}" class="btn btn-outline-secondary flex-grow-1"><i class="fas fa-redo"></i></a></div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-sm-12">
                <div class="card card-table">
                    <div class="card-header"><h5 class="card-title">طلبات جاهزة لإدخال النتائج ({{ $labRequests->total() }})</h5></div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-center mb-0">
                                <thead>
                                    <tr>
                                        <th># الطلب</th><th>المريض</th><th>رقم الملف</th><th>الطبيب الطالب</th><th>تاريخ الطلب</th><th class="text-center">الحالة</th><th class="text-end">الإجراء</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($labRequests as $labRequest)
                                    <tr>
                                        <td>{{ $labRequest->id }}</td>
                                        <td>{{ $labRequest->patient->full_name }}</td>
                                        <td>{{ $labRequest->patient->file_number }}</td>
                                        <td>{{ $labRequest->doctor->name }}</td>
                                        <td>{{ $labRequest->request_date->format('Y-m-d H:i') }}</td>
                                        <td class="text-center"><span class="badge bg-info-light">{{ str_replace('_', ' ', $labRequest->status) }}</span></td>
                                        <td class="text-end">
                                            <a href="{{ route('lab.results.entry_form', $labRequest->id) }}" class="btn btn-sm btn-success"><i class="fas fa-edit me-1"></i> إدخال النتائج</a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="7" class="text-center py-4"><h5>لا توجد طلبات تنتظر النتائج حالياً</h5></td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if ($labRequests->hasPages())
                        <div class="mt-4 d-flex justify-content-center">{{ $labRequests->appends(request()->query())->links('pagination::bootstrap-5') }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection