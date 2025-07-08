@extends('layout.mainlayout')
@section('title', 'طلبات الفحص المخبري - ' . $patient->full_name)

@push('styles')
    {{-- يمكن إضافة مكتبات CSS هنا إذا احتجت لواجهات متقدمة مثل Select2 --}}
@endpush

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">

        {{-- Page Header --}}
        @component('components.page-header')
            @slot('title') الملف السريري للمريض: {{ $patient->full_name }} @endslot
            @slot('li_1') <a href="{{ route('patient_management.patients.show', $patient->id) }}">ملف المريض</a> @endslot
            @slot('li_2') طلبات الفحص المخبري @endslot
        @endcomponent
        {{-- /Page Header --}}

        <div class="row mb-3">
            <div class="col">
                <a href="{{ route('patient_management.patients.show', $patient->id) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-right"></i> العودة لملف المريض
                </a>
            </div>
            <div class="col text-end">
                {{-- صلاحية إنشاء الطلب تكون عادة للطبيب --}}
                @can('create lab requests')
                <a href="{{ route('clinical.lab_requests.create', $patient->id) }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> إنشاء طلب فحص جديد
                </a>
                @endcan
            </div>
        </div>

        {{-- Filter Card --}}
        <div class="card">
            <div class="card-header">
                <h5 class="card-title"><i class="fas fa-filter me-2"></i> فلترة الطلبات</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('clinical.lab_requests.index', $patient->id) }}" method="GET">
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <label for="status" class="form-label">الحالة</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">جميع الحالات</option>
                                @foreach($statuses as $key => $value)
                                    <option value="{{ $key }}" @selected(request('status') == $key)>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="doctor_id" class="form-label">الطبيب الطالب</label>
                            <select name="doctor_id" id="doctor_id" class="form-select">
                                <option value="">جميع الأطباء</option>
                                @foreach($doctors as $id => $name)
                                    <option value="{{ $id }}" @selected(request('doctor_id') == $id)>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 d-flex mt-4">
                            <button type="submit" class="btn btn-primary flex-grow-1 me-2"><i class="fas fa-search"></i> بحث</button>
                            <a href="{{ route('clinical.lab_requests.index', $patient->id) }}" class="btn btn-outline-secondary flex-grow-1"><i class="fas fa-redo"></i> إعادة تعيين</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        {{-- /Filter Card --}}


        <div class="row mt-4">
            <div class="col-sm-12">
                <div class="card card-table">
                    <div class="card-header">
                        <h5 class="card-title">قائمة الطلبات ({{ $labRequests->total() }})</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-center mb-0">
                                <thead>
                                    <tr>
                                        <th># الطلب</th>
                                        <th>الطبيب</th>
                                        <th>تاريخ الطلب</th>
                                        <th>تاريخ النتيجة</th>
                                        <th class="text-center">الحالة</th>
                                        <th class="text-end">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($labRequests as $request)
                                    <tr>
                                        <td>{{ $request->id }}</td>
                                        <td>{{ $request->doctor->name ?? 'غير محدد' }}</td>
                                        <td>{{ $request->request_date->format('Y-m-d h:i A') }}</td>
                                        <td>{{ $request->result_date ? $request->result_date->format('Y-m-d h:i A') : 'لم تصدر بعد' }}</td>
                                        <td class="text-center">
                                            @php
                                                $statusClasses = [
                                                    'pending_sample' => 'bg-warning-light',
                                                    'sample_collected' => 'bg-info-light',
                                                    'processing' => 'bg-primary-light',
                                                    'completed' => 'bg-success-light',
                                                    'cancelled' => 'bg-danger-light',
                                                ];
                                                $statusClass = $statusClasses[$request->status] ?? 'bg-secondary-light';
                                            @endphp
                                            <span class="badge {{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $request->status)) }}</span>
                                        </td>
                                        <td class="text-end">
                                            <div class="actions">
                                                <a href="{{ route('clinical.lab_requests.show', [$patient->id, $request->id]) }}" class="btn btn-sm bg-success-light" title="عرض التفاصيل">
                                                    <i class="far fa-eye"></i>
                                                </a>
                                                @if(!in_array($request->status, ['completed', 'cancelled']))
                                                    <a href="{{ route('clinical.lab_requests.edit', [$patient->id, $request->id]) }}" class="btn btn-sm bg-warning-light" title="تعديل الطلب">
                                                        <i class="fas fa-pen"></i>
                                                    </a>
                                                    {{-- صلاحية الحذف تكون لمسؤول النظام أو الطبيب الذي أنشأ الطلب --}}
                                                    <form action="{{ route('clinical.lab_requests.destroy', [$patient->id, $request->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الطلب؟')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm bg-danger-light" title="حذف الطلب"><i class="far fa-trash-alt"></i></button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <p class="text-muted">لا توجد طلبات فحص مخبري لهذا المريض حالياً.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($labRequests->hasPages())
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $labRequests->appends(request()->query())->links() }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection