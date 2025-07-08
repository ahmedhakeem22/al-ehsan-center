@extends('layout.mainlayout')
@section('title', 'الوصفات الطبية للمريض: ' . $patient->full_name)

@section('content')
    <div class="page-wrapper">
        <div class="content">
            @component('components.page-header')
                @slot('title')
                    الوصفات الطبية للمريض: <a href="{{ route('patient_management.patients.show', $patient->id) }}">{{ $patient->full_name }}</a>
                @endslot
                @slot('li_1')
                    الوصفات الطبية
                @endslot
            @endcomponent

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">فلترة الوصفات</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('clinical.prescriptions.index', $patient->id) }}" method="GET">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>الخطة العلاجية</label>
                                            <select name="treatment_plan_id" class="form-control">
                                                <option value="">كل الخطط</option>
                                                @foreach($treatmentPlans as $id => $diagnosis)
                                                    <option value="{{ $id }}" {{ request('treatment_plan_id') == $id ? 'selected' : '' }}>
                                                        {{ \Illuminate\Support\Str::limit($diagnosis, 50) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>الحالة</label>
                                            <select name="status" class="form-control">
                                                <option value="">كل الحالات</option>
                                                @foreach($statuses as $key => $value)
                                                    <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                                        {{ $value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4 align-self-end">
                                        <button type="submit" class="btn btn-primary">بحث</button>
                                        <a href="{{ route('clinical.prescriptions.index', $patient->id) }}" class="btn btn-secondary">إعادة تعيين</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <a href="{{ route('clinical.prescriptions.create', $patient->id) }}" class="btn btn-primary mb-3"><i class="fe fe-plus"></i> إضافة وصفة جديدة</a>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>تاريخ الوصفة</th>
                                            <th>الطبيب</th>
                                            <th>التشخيص (الخطة العلاجية)</th>
                                            <th>الحالة</th>
                                            <th>إجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($prescriptions as $prescription)
                                            <tr>
                                                <td>{{ $prescription->id }}</td>
                                                <td>{{ \Carbon\Carbon::parse($prescription->prescription_date)->format('Y-m-d H:i') }}</td>
                                                <td>{{ $prescription->doctor->name ?? 'غير محدد' }}</td>
                                                <td>{{ $prescription->treatmentPlan->diagnosis ?? 'لا يوجد' }}</td>
                                                <td>
                                                    @if($prescription->status == 'pending') <span class="badge bg-warning">قيد الانتظار</span>
                                                    @elseif($prescription->status == 'dispensed') <span class="badge bg-success">تم الصرف</span>
                                                    @elseif($prescription->status == 'cancelled') <span class="badge bg-danger">ملغاة</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('clinical.prescriptions.show', [$patient->id, $prescription->id]) }}" class="btn btn-sm btn-outline-primary">عرض</a>
                                                    @if($prescription->status !== 'dispensed')
                                                    <a href="{{ route('clinical.prescriptions.edit', [$patient->id, $prescription->id]) }}" class="btn btn-sm btn-outline-secondary">تعديل</a>
                                                    <form action="{{ route('clinical.prescriptions.destroy', [$patient->id, $prescription->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من رغبتك في حذف هذه الوصفة؟');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">حذف</button>
                                                    </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">لا توجد وصفات طبية مسجلة لهذا المريض.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                {{ $prescriptions->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection