@extends('layout.mainlayout')
@section('title', 'تعديل طلب فحص مخبري #' . $labRequest->id)

@push('styles')
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" /> --}}
@endpush

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">

        @component('components.page-header')
            @slot('title') تعديل طلب فحص مخبري @endslot
            @slot('li_1') <a href="{{ route('clinical.lab_requests.index', $patient->id) }}">قائمة الطلبات</a> @endslot
            @slot('li_2') تعديل الطلب رقم #{{ $labRequest->id }} @endslot
        @endcomponent

        <div class="row">
            <div class="col-md-10 offset-md-1">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">تعديل بيانات الطلب</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('clinical.lab_requests.update', [$patient->id, $labRequest->id]) }}" method="POST">
                            @csrf
                            @method('PUT')
                             <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="request_date" class="form-label">تاريخ ووقت الطلب <span class="text-danger">*</span></label>
                                    <input type="datetime-local" id="request_date" name="request_date" class="form-control @error('request_date') is-invalid @enderror"
                                           value="{{ old('request_date', $labRequest->request_date->format('Y-m-d\TH:i')) }}" required>
                                    @error('request_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">الحالة <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                        @foreach($statuses as $key => $value)
                                            <option value="{{ $key }}" @selected(old('status', $labRequest->status) == $key)>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                     @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="requested_tests" class="form-label">الفحوصات المطلوبة <span class="text-danger">*</span></label>
                                    <select name="requested_tests[]" id="requested_tests" class="form-select @error('requested_tests') is-invalid @enderror" multiple required>
                                        @foreach($availableTests as $test)
                                            <option value="{{ $test->id }}" @selected(in_array($test->id, old('requested_tests', $selectedTests)))>
                                                {{ $test->name }} ({{ $test->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">يمكنك اختيار أكثر من فحص بالضغط على Ctrl أو Command.</small>
                                    @error('requested_tests') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="notes_from_doctor" class="form-label">ملاحظات الطبيب</label>
                                    <textarea name="notes_from_doctor" id="notes_from_doctor" class="form-control @error('notes_from_doctor') is-invalid @enderror" rows="4">{{ old('notes_from_doctor', $labRequest->notes_from_doctor) }}</textarea>
                                    @error('notes_from_doctor') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="text-end mt-4">
                                <a href="{{ route('clinical.lab_requests.show', [$patient->id, $labRequest->id]) }}" class="btn btn-secondary">إلغاء</a>
                                <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    {{-- <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var multipleCancelButton = new Choices('#requested_tests', {
                removeItemButton: true,
            });
        });
    </script> --}}
@endpush