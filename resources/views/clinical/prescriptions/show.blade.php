@extends('layout.mainlayout')
@section('title', 'تفاصيل الوصفة الطبية #' . $prescription->id)

@section('content')
<div class="page-wrapper">
    <div class="content">
        @component('components.page-header')
            @slot('title')
                الوصفة الطبية #{{ $prescription->id }}
            @endslot
            @slot('li_1')
                للمريض: <a href="{{ route('patient_management.patients.show', $patient->id) }}">{{ $patient->full_name }}</a>
            @endslot
        @endcomponent

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">تفاصيل الوصفة</h4>
                        <div>
                            @if($prescription->status !== 'dispensed')
                            <a href="{{ route('clinical.prescriptions.edit', [$patient->id, $prescription->id]) }}" class="btn btn-secondary btn-sm"><i class="fe fe-edit"></i> تعديل</a>
                            @endif
                            {{-- Add Print Button later --}}
                            <button onclick="window.print()" class="btn btn-info btn-sm"><i class="fe fe-printer"></i> طباعة</button>
                        </div>
                    </div>
                    <div class="card-body">
                        {{-- Main Info --}}
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <p><strong>رقم الوصفة:</strong> {{ $prescription->id }}</p>
                                <p><strong>المريض:</strong> {{ $prescription->patient->full_name }} ({{ $prescription->patient->file_number }})</p>
                                <p><strong>الطبيب:</strong> {{ $prescription->doctor->name ?? 'غير محدد' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>تاريخ الوصفة:</strong> {{ \Carbon\Carbon::parse($prescription->prescription_date)->format('Y-m-d H:i') }}</p>
                                <p><strong>الحالة:</strong>
                                    @if($prescription->status == 'pending') <span class="badge bg-warning fs-6">قيد الانتظار</span>
                                    @elseif($prescription->status == 'dispensed') <span class="badge bg-success fs-6">تم الصرف</span>
                                    @elseif($prescription->status == 'cancelled') <span class="badge bg-danger fs-6">ملغاة</span>
                                    @endif
                                </p>
                                <p><strong>الخطة العلاجية:</strong> {{ $prescription->treatmentPlan->diagnosis ?? 'لا يوجد' }}</p>
                            </div>
                            @if($prescription->notes)
                            <div class="col-12">
                                <p><strong>ملاحظات الطبيب:</strong></p>
                                <p class="border p-2 rounded bg-light">{{ $prescription->notes }}</p>
                            </div>
                            @endif
                        </div>

                        {{-- Prescription Items --}}
                        <h5 class="mb-3">الأدوية الموصوفة</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>الدواء</th>
                                        <th>الجرعة</th>
                                        <th>التكرار</th>
                                        <th>المدة</th>
                                        <th>الكمية الموصوفة</th>
                                        <th>الكمية المصروفة</th>
                                        <th>تعليمات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($prescription->items as $item)
                                    <tr>
                                        <td>{{ $item->medication->name ?? $item->medication_name_manual }}</td>
                                        <td>{{ $item->dosage }}</td>
                                        <td>{{ $item->frequency }}</td>
                                        <td>{{ $item->duration }}</td>
                                        <td>{{ $item->quantity_prescribed ?? 'N/A' }}</td>
                                        <td>{{ $item->quantity_dispensed ?? 'لم تصرف بعد' }}</td>
                                        <td>{{ $item->instructions ?? 'لا يوجد' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <hr>

                        {{-- Dispensing Section --}}
                        @if($prescription->status == 'pending' && auth()->user()->role->name === 'Pharmacist')
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h5 class="card-title mb-0 text-white">صرف الوصفة</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('clinical.prescriptions.dispense', [$patient->id, $prescription->id]) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <p class="text-muted">الرجاء إدخال الكمية المصروفة لكل دواء.</p>
                                    @foreach($prescription->items as $item)
                                        <div class="row mb-2 align-items-center">
                                            <div class="col-md-6">
                                                <label for="item_{{ $item->id }}_quantity" class="form-label">
                                                    <strong>{{ $item->medication->name ?? $item->medication_name_manual }}:</strong>
                                                </label>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="number" name="items[{{ $item->id }}][quantity_dispensed]" id="item_{{ $item->id }}_quantity"
                                                       value="{{ old('items.'.$item->id.'.quantity_dispensed', $item->quantity_prescribed) }}"
                                                       class="form-control" placeholder="الكمية المصروفة" required min="0">
                                            </div>
                                        </div>
                                    @endforeach
                                    <div class="mt-3">
                                        <label for="dispensing_notes" class="form-label">ملاحظات الصرف (اختياري)</label>
                                        <textarea name="dispensing_notes" id="dispensing_notes" class="form-control" rows="2">{{ old('dispensing_notes') }}</textarea>
                                    </div>
                                    <div class="text-end mt-3">
                                        <button type="submit" class="btn btn-success"><i class="fe fe-check-circle"></i> تأكيد الصرف</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @elseif($prescription->status == 'dispensed')
                        <div class="alert alert-success">
                            <h5 class="alert-heading">معلومات الصرف</h5>
                            <p>تم صرف هذه الوصفة بواسطة <strong>{{ $prescription->pharmacist->name ?? 'غير معروف' }}</strong>
                               بتاريخ: <strong>{{ \Carbon\Carbon::parse($prescription->dispensing_date)->format('Y-m-d H:i') }}</strong>.</p>
                        </div>
                        @endif
                    </div>
                    <div class="card-footer text-muted">
                        <a href="{{ route('clinical.prescriptions.index', $patient->id) }}" class="btn btn-outline-secondary">العودة إلى قائمة الوصفات</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection