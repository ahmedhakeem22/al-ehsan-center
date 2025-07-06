@extends('layout.mainlayout')
@section('title', 'صرف وصفة طبية رقم ' . $prescription->id)

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">

        @component('components.page-header')
            @slot('title')
                صرف الوصفة الطبية
            @endslot
            @slot('li_1')
                وصفة رقم #{{ $prescription->id }}
            @endslot
        @endcomponent

        <form action="{{ route('pharmacy.dispense.process', $prescription->id) }}" method="POST">
            @csrf
            
            {{-- معلومات الوصفة --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">معلومات الوصفة الأساسية</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <strong>رقم الوصفة:</strong> #{{ $prescription->id }}
                        </div>
                        <div class="col-md-4">
                            <strong>المريض:</strong> {{ $prescription->patient->full_name }} ({{ $prescription->patient->file_number }})
                        </div>
                        <div class="col-md-4">
                            <strong>الطبيب:</strong> {{ $prescription->doctor->name }}
                        </div>
                        <div class="col-md-4 mt-2">
                            <strong>تاريخ الوصفة:</strong> {{ $prescription->prescription_date->format('Y-m-d') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- قائمة الأدوية للصرف --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">الأدوية الموصوفة</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>الدواء</th>
                                    <th>الجرعة والتعليمات</th>
                                    <th style="width: 150px;">الكمية الموصوفة</th>
                                    <th style="width: 150px;">الكمية المصروفة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($prescription->items as $item)
                                    <tr>
                                        <td>
                                            <strong>{{ $item->medication->name ?? $item->medication_name_manual }}</strong>
                                            <small class="d-block text-muted">{{ $item->medication->strength ?? '' }}</small>
                                        </td>
                                        <td>
                                            <p class="mb-1"><strong>الجرعة:</strong> {{ $item->dosage }}</p>
                                            <p class="mb-1"><strong>التكرار:</strong> {{ $item->frequency }}</p>
                                            <p class="mb-1"><strong>لمدة:</strong> {{ $item->duration }}</p>
                                            @if($item->instructions)
                                            <p class="mb-0 text-info"><strong>تعليمات:</strong> {{ $item->instructions }}</p>
                                            @endif
                                        </td>
                                        <td class="text-center align-middle">
                                            <span class="badge bg-primary fs-6">{{ $item->quantity_prescribed }}</span>
                                        </td>
                                        <td class="align-middle">
                                            <input type="number" 
                                                   name="items[{{ $item->id }}][quantity_dispensed]" 
                                                   class="form-control text-center @error('items.'.$item->id.'.quantity_dispensed') is-invalid @enderror" 
                                                   value="{{ old('items.'.$item->id.'.quantity_dispensed', $item->quantity_prescribed) }}"
                                                   min="0"
                                                   required>
                                            @error('items.'.$item->id.'.quantity_dispensed')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            {{-- ملاحظات الصيدلي وزر التأكيد --}}
            <div class="card">
                 <div class="card-header">
                    <h5 class="card-title">ملاحظات الصيدلي وتأكيد الصرف</h5>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="dispensing_notes" class="form-label">ملاحظات إضافية (اختياري)</label>
                        <textarea name="dispensing_notes" id="dispensing_notes" class="form-control" rows="3">{{ old('dispensing_notes') }}</textarea>
                    </div>
                    <div class="text-end mt-4">
                        <a href="{{ route('pharmacy.dispense.index') }}" class="btn btn-secondary">العودة لقائمة الوصفات</a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check-circle me-1"></i> تأكيد وإتمام الصرف
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection