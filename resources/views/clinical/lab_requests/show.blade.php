@extends('layout.mainlayout')
@section('title', 'عرض طلب فحص مخبري #' . $labRequest->id)

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">

        @component('components.page-header')
            @slot('title') عرض طلب فحص مخبري @endslot
            @slot('li_1') <a href="{{ route('clinical.lab_requests.index', $patient->id) }}">قائمة الطلبات</a> @endslot
            @slot('li_2') طلب رقم #{{ $labRequest->id }} @endslot
        @endcomponent

        <div class="row mb-3">
            <div class="col">
                <a href="{{ route('clinical.lab_requests.index', $patient->id) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-right me-1"></i> العودة لقائمة الطلبات
                </a>
            </div>
            <div class="col text-end">
                @can('enter lab results')
                    @if(in_array($labRequest->status, ['sample_collected', 'processing']))
                        <a href="{{ route('clinical.lab_requests.enter_results_form', [$patient->id, $labRequest->id]) }}" class="btn btn-success">
                            <i class="fas fa-vial me-2"></i> إدخال النتائج
                        </a>
                    @endif
                @endcan
            </div>
        </div>

        {{-- Request Details Card --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">معلومات الطلب</h5>
                @php
                    $statusClasses = [
                        'pending_sample' => 'bg-warning-light', 'sample_collected' => 'bg-info-light',
                        'processing' => 'bg-primary-light', 'completed' => 'bg-success-light',
                        'cancelled' => 'bg-danger-light',
                    ];
                    $statusClass = $statusClasses[$labRequest->status] ?? 'bg-secondary-light';
                @endphp
                <span class="badge {{ $statusClass }} p-2">{{ ucfirst(str_replace('_', ' ', $labRequest->status)) }}</span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 col-lg-4 mb-3">
                        <strong>المريض:</strong>
                        <p class="text-muted">{{ $labRequest->patient->full_name }} (#{{ $labRequest->patient->file_number }})</p>
                    </div>
                    <div class="col-md-6 col-lg-4 mb-3">
                        <strong>الطبيب الطالب:</strong>
                        <p class="text-muted">{{ $labRequest->doctor->name }}</p>
                    </div>
                    <div class="col-md-6 col-lg-4 mb-3">
                        <strong>تاريخ الطلب:</strong>
                        <p class="text-muted">{{ $labRequest->request_date->format('Y-m-d, h:i A') }}</p>
                    </div>
                    @if($labRequest->status == 'completed')
                        <div class="col-md-6 col-lg-4 mb-3">
                            <strong>فني المختبر:</strong>
                            <p class="text-muted">{{ $labRequest->labTechnician->name ?? 'غير محدد' }}</p>
                        </div>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <strong>تاريخ النتيجة:</strong>
                            <p class="text-muted">{{ $labRequest->result_date ? $labRequest->result_date->format('Y-m-d, h:i A') : '-' }}</p>
                        </div>
                    @endif
                    @if($labRequest->notes_from_doctor)
                        <div class="col-12">
                            <strong>ملاحظات الطبيب:</strong>
                            <p class="text-muted fst-italic bg-light p-2 rounded">{{ $labRequest->notes_from_doctor }}</p>
                        </div>
                    @endif
                    @if($labRequest->notes_from_lab)
                        <div class="col-12">
                            <strong>ملاحظات المختبر:</strong>
                            <p class="text-muted fst-italic bg-light p-2 rounded">{{ $labRequest->notes_from_lab }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        {{-- /Request Details Card --}}

        {{-- Results Card --}}
        <div class="card">
            <div class="card-header"><h5 class="card-title">الفحوصات المطلوبة والنتائج</h5></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>الفحص</th>
                                <th>المدى المرجعي</th>
                                <th>النتيجة</th>
                                <th>الوحدة</th>
                                <th class="text-center">الحالة</th>
                                <th>ملاحظات الفحص</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($labRequest->items as $item)
                            <tr class="{{ $item->is_abnormal ? 'table-danger' : '' }}">
                                <td><strong>{{ $item->availableLabTest->name }}</strong></td>
                                <td>{{ $item->availableLabTest->reference_range ?? 'N/A' }}</td>
                                <td>{{ $item->result_value ?? 'لم تصدر' }}</td>
                                <td>{{ $item->result_unit ?? '-' }}</td>
                                <td class="text-center">
                                    @if(isset($item->is_abnormal))
                                        @if($item->is_abnormal)
                                            <span class="badge bg-danger">غير طبيعي</span>
                                        @else
                                            <span class="badge bg-success">طبيعي</span>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $item->notes ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center">لم يتم تحديد فحوصات في هذا الطلب.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{-- /Results Card --}}
    </div>
</div>
@endsection