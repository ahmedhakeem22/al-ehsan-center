@extends('layout.mainlayout')
@section('title', 'الخطط العلاجية - ' . $patient->full_name)

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">

        @component('components.page-header')
            @slot('title') الملف السريري للمريض: {{ $patient->full_name }} @endslot
            @slot('li_1') <a href="{{ route('patient_management.patients.show', $patient->id) }}">ملف المريض</a> @endslot
            @slot('li_2') الخطط العلاجية @endslot
        @endcomponent

        <div class="row mb-3">
            <div class="col">
                <a href="{{ route('patient_management.patients.show', $patient->id) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-right"></i> العودة لملف المريض
                </a>
            </div>
            <div class="col text-end">
                <a href="{{ route('clinical.treatment_plans.create', $patient->id) }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> إنشاء خطة علاجية جديدة
                </a>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-sm-12">
                <div class="card card-table">
                    <div class="card-header">
                        <h5 class="card-title">قائمة الخطط العلاجية ({{ $plans->total() }})</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-center mb-0">
                                <thead>
                                    <tr>
                                        <th># الخطة</th>
                                        <th>التشخيص</th>
                                        <th>الطبيب</th>
                                        <th>تاريخ البدء</th>
                                        <th>تاريخ الانتهاء</th>
                                        <th class="text-center">الحالة</th>
                                        <th class="text-end">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($plans as $plan)
                                    <tr>
                                        <td>{{ $plan->id }}</td>
                                        <td>{{ Str::limit($plan->diagnosis, 40) }}</td>
                                        <td>{{ $plan->doctor->name ?? 'غير محدد' }}</td>
                                        <td>{{ $plan->start_date->format('Y-m-d') }}</td>
                                        <td>{{ $plan->end_date ? $plan->end_date->format('Y-m-d') : 'مستمرة' }}</td>
                                        <td class="text-center">
                                            @php
                                                $statusClasses = [
                                                    'active' => 'bg-success-light',
                                                    'completed' => 'bg-info-light',
                                                    'on_hold' => 'bg-warning-light',
                                                    'cancelled' => 'bg-danger-light',
                                                ];
                                                $statusText = [
                                                    'active' => 'نشطة',
                                                    'completed' => 'مكتملة',
                                                    'on_hold' => 'معلقة',
                                                    'cancelled' => 'ملغاة',
                                                ];
                                            @endphp
                                            <span class="badge {{ $statusClasses[$plan->status] ?? 'bg-secondary-light' }}">{{ $statusText[$plan->status] ?? $plan->status }}</span>
                                        </td>
                                        <td class="text-end">
                                            <div class="actions">
                                                <a href="{{ route('clinical.treatment_plans.show', [$patient->id, $plan->id]) }}" class="btn btn-sm bg-success-light" title="عرض التفاصيل"><i class="far fa-eye"></i></a>
                                                <a href="{{ route('clinical.treatment_plans.edit', [$patient->id, $plan->id]) }}" class="btn btn-sm bg-warning-light" title="تعديل"><i class="fas fa-pen"></i></a>
                                                <form action="{{ route('clinical.treatment_plans.destroy', [$patient->id, $plan->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الخطة؟ سيؤثر هذا على الوصفات المرتبطة بها.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm bg-danger-light" title="حذف"><i class="far fa-trash-alt"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <p class="text-muted">لا توجد خطط علاجية مسجلة لهذا المريض.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if ($plans->hasPages())
                        <div class="mt-4 d-flex justify-content-center">{{ $plans->links() }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection