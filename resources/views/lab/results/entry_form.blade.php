@extends('layout.mainlayout')
@section('title', 'إدخال نتائج طلب #' . $labRequest->id)

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        @component('components.page-header')
            @slot('title') إدخال النتائج @endslot
            @slot('li_1') طلب فحص رقم #{{ $labRequest->id }} @endslot
        @endcomponent

        <form action="{{ route('lab.results.save', $labRequest->id) }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-header"><h5 class="card-title">معلومات الطلب</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4"><strong>المريض:</strong> {{ $labRequest->patient->full_name }}</div>
                        <div class="col-md-4"><strong>رقم الملف:</strong> {{ $labRequest->patient->file_number }}</div>
                        <div class="col-md-4"><strong>الطبيب:</strong> {{ $labRequest->doctor->name }}</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5 class="card-title">إدخال النتائج</h5></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>الفحص المطلوب</th>
                                    <th>المدى المرجعي</th>
                                    <th style="width: 15%;">النتيجة</th>
                                    <th style="width: 10%;">الوحدة</th>
                                    <th style="width: 10%;" class="text-center">غير طبيعي؟</th>
                                    <th>ملاحظات على الفحص</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($labRequest->items as $item)
                                <tr>
                                    <td><strong>{{ $item->availableLabTest->name }}</strong></td>
                                    <td>{{ $item->availableLabTest->reference_range ?? 'N/A' }}</td>
                                    <td><input type="text" name="results[{{ $item->id }}][result_value]" class="form-control" value="{{ old('results.'.$item->id.'.result_value') }}"></td>
                                    <td><input type="text" name="results[{{ $item->id }}][result_unit]" class="form-control" value="{{ old('results.'.$item->id.'.result_unit') }}"></td>
                                    <td class="text-center align-middle">
                                        <div class="form-check form-switch d-flex justify-content-center">
                                            <input class="form-check-input" type="checkbox" name="results[{{ $item->id }}][is_abnormal]" value="1" @checked(old('results.'.$item->id.'.is_abnormal'))>
                                        </div>
                                    </td>
                                    <td><input type="text" name="results[{{ $item->id }}][notes]" class="form-control" value="{{ old('results.'.$item->id.'.notes') }}"></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5 class="card-title">ملاحظات عامة وتأكيد</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <label for="notes_from_lab" class="form-label">ملاحظات عامة من المختبر على الطلب</label>
                            <textarea name="notes_from_lab" id="notes_from_lab" class="form-control" rows="3">{{ old('notes_from_lab') }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <label for="result_date" class="form-label">تاريخ ووقت النتيجة <span class="text-danger">*</span></label>
                            <input type="datetime-local" id="result_date" name="result_date" class="form-control @error('result_date') is-invalid @enderror" value="{{ old('result_date', now()->format('Y-m-d\TH:i')) }}" required>
                            @error('result_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="text-end mt-4">
                        <a href="{{ route('lab.results.index') }}" class="btn btn-secondary">إلغاء</a>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> حفظ وإتمام النتائج</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection