@extends('layout.mainlayout')
@section('title', 'إضافة ملاحظة سريرية جديدة')

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        @component('components.page-header')
            @slot('title') إضافة ملاحظة لـ: {{ $patient->full_name }} @endslot
            @slot('li_1') <a href="{{ route('patient_management.patients.show', [$patient->id, '#pat_clinical_notes']) }}">الملاحظات السريرية</a> @endslot
            @slot('li_2') ملاحظة جديدة @endslot
        @endcomponent

        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header"><h5 class="card-title">تفاصيل الملاحظة</h5></div>
                    <div class="card-body">
                        <form action="{{ route('clinical.notes.store', $patient->id) }}" method="POST">
                            {{-- Include the form partial --}}
                            @include('clinical.notes._form')

                            <div class="text-end mt-4">
                                <a href="{{ route('patient_management.patients.show', [$patient->id, '#pat_clinical_notes']) }}" class="btn btn-secondary">إلغاء</a>
                                <button type="submit" class="btn btn-primary">حفظ الملاحظة</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection