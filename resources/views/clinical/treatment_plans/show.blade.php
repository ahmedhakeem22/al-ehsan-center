@extends('layout.mainlayout')
@section('title', 'عرض الخطة العلاجية #' . $plan->id)

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">

        @component('components.page-header')
            @slot('title') عرض الخطة العلاجية @endslot
            @slot('li_1') <a href="{{ route('clinical.treatment_plans.index', $patient->id) }}">قائمة الخطط</a> @endslot
            @slot('li_2') خطة رقم #{{ $plan->id }} @endslot
        @endcomponent

        <div class="row mb-3">
            <div class="col-md-6">
                <a href="{{ route('clinical.treatment_plans.index', $patient->id) }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-right me-1"></i> العودة لقائمة الخطط</a>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('clinical.treatment_plans.edit', [$patient->id, $plan->id]) }}" class="btn btn-warning"><i class="fas fa-pen me-1"></i> تعديل الخطة</a>
                <a href="{{ route('clinical.prescriptions.create', ['patient' => $patient->id, 'treatment_plan_id' => $plan->id]) }}" class="btn btn-success"><i class="fas fa-pills me-1"></i> إنشاء وصفة مرتبطة</a>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">التشخيص: {{ $plan->diagnosis }}</h4>
                @php
                    $statusClasses = ['active' => 'bg-success', 'completed' => 'bg-info', 'on_hold' => 'bg-warning', 'cancelled' => 'bg-danger'];
                    $statusText = ['active' => 'نشطة', 'completed' => 'مكتملة', 'on_hold' => 'معلقة', 'cancelled' => 'ملغاة'];
                @endphp
                <span class="badge {{ $statusClasses[$plan->status] ?? 'bg-secondary' }} p-2">{{ $statusText[$plan->status] ?? $plan->status }}</span>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-lg-3 col-md-6"><strong>المريض:</strong><p class="text-muted">{{ $patient->full_name }}</p></div>
                    <div class="col-lg-3 col-md-6"><strong>الطبيب:</strong><p class="text-muted">{{ $plan->doctor->name }}</p></div>
                    <div class="col-lg-3 col-md-6"><strong>تاريخ البدء:</strong><p class="text-muted">{{ $plan->start_date->format('d M, Y') }}</p></div>
                    <div class="col-lg-3 col-md-6"><strong>تاريخ الانتهاء:</strong><p class="text-muted">{{ $plan->end_date ? $plan->end_date->format('d M, Y') : 'مستمرة' }}</p></div>
                </div>

                <hr>

                <h5>تفاصيل الخطة</h5>
                <div class="p-3 bg-light rounded">
                    {!! nl2br(e($plan->plan_details)) ?: '<p class="text-muted">لا توجد تفاصيل إضافية مسجلة.</p>' !!}
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h5 class="card-title">الوصفات الطبية المرتبطة بهذه الخطة</h5></div>
            <div class="card-body">
                @if($plan->prescriptions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr><th># الوصفة</th><th>تاريخ الوصفة</th><th>الطبيب</th><th class="text-center">الحالة</th><th class="text-end">الإجراء</th></tr>
                        </thead>
                        <tbody>
                            @foreach($plan->prescriptions as $prescription)
                            <tr>
                                <td>{{ $prescription->id }}</td>
                                <td>{{ $prescription->prescription_date->format('Y-m-d') }}</td>
                                <td>{{ $prescription->doctor->name }}</td>
                                <td class="text-center">
                                     @php
                                        $pStatusClasses = ['pending' => 'bg-warning-light', 'dispensed' => 'bg-success-light', 'cancelled' => 'bg-danger-light'];
                                        $pStatusText = ['pending' => 'قيد الانتظار', 'dispensed' => 'تم الصرف', 'cancelled' => 'ملغاة'];
                                    @endphp
                                    <span class="badge {{ $pStatusClasses[$prescription->status] ?? '' }}">{{ $pStatusText[$prescription->status] ?? '' }}</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('clinical.prescriptions.show', [$patient->id, $prescription->id]) }}" class="btn btn-sm btn-outline-primary">عرض الوصفة</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-center text-muted py-3">لا توجد وصفات طبية مرتبطة بهذه الخطة حتى الآن.</p>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection