@extends('layout.mainlayout')

@section('title', 'تفاصيل الدواء')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">تفاصيل الدواء: {{ $medication->name }}</h4>
                    <div class="float-end">
                        <a href="{{ route('pharmacy.medications.edit', $medication) }}" class="btn btn-primary">
                            <i class="fa fa-edit"></i> تعديل
                        </a>
                        <a href="{{ route('pharmacy.medications.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> العودة للقائمة
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 30%">اسم الدواء</th>
                                    <td>{{ $medication->name }}</td>
                                </tr>
                                <tr>
                                    <th>الاسم العام</th>
                                    <td>{{ $medication->generic_name ?: 'غير محدد' }}</td>
                                </tr>
                                <tr>
                                    <th>الشركة المصنعة</th>
                                    <td>{{ $medication->manufacturer ?: 'غير محدد' }}</td>
                                </tr>
                                <tr>
                                    <th>الشكل الدوائي</th>
                                    <td>{{ $medication->form ?: 'غير محدد' }}</td>
                                </tr>
                                <tr>
                                    <th>التركيز</th>
                                    <td>{{ $medication->strength ?: 'غير محدد' }}</td>
                                </tr>
                                <tr>
                                    <th>ملاحظات</th>
                                    <td>{{ $medication->notes ?: 'لا توجد ملاحظات' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5>تاريخ الوصفات الطبية</h5>
                            @if($medication->prescriptionItems->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>رقم الوصفة</th>
                                                <th>المريض</th>
                                                <th>الطبيب</th>
                                                <th>تاريخ الوصفة</th>
                                                <th>الجرعة</th>
                                                <th>التعليمات</th>
                                                <th>الكمية</th>
                                                <th>الحالة</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($medication->prescriptionItems as $item)
                                                <tr>
                                                    <td>{{ $item->prescription_id }}</td>
                                                    <td>{{ $item->prescription->patient->full_name }}</td>
                                                    <td>{{ $item->prescription->doctor->name }}</td>
                                                    <td>{{ $item->prescription->prescription_date->format('Y-m-d') }}</td>
                                                    <td>{{ $item->dosage }}</td>
                                                    <td>{{ $item->instructions }}</td>
                                                    <td>{{ $item->quantity_prescribed }}</td>
                                                    <td>
                                                        @if($item->prescription->status == 'pending')
                                                            <span class="badge bg-warning">قيد الانتظار</span>
                                                        @elseif($item->prescription->status == 'dispensed')
                                                            <span class="badge bg-success">تم الصرف</span>
                                                        @else
                                                            <span class="badge bg-secondary">{{ $item->prescription->status }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p>لم يتم استخدام هذا الدواء في أي وصفات طبية حتى الآن.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection