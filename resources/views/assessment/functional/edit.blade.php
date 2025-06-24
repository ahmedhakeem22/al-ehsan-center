@php $page = 'assessment-edit'; @endphp
@extends('layout.mainlayout')
@section('title', 'تعديل تقييم لـ: ' . $patient->full_name)

@section('content')
    <div class="page-wrapper">
        <div class="content">
            @component('components.page-header')
                @slot('title')
                    تقييم التحسن الوظيفي
                @endslot
                @slot('li_1')
                    <a href="{{ route('patient_management.patients.show', $patient->id) }}">{{ $patient->full_name }} ({{$patient->file_number}})</a>
                @endslot
                @slot('li_2')
                    تعديل التقييم بتاريخ: {{ $assessment->assessment_date_gregorian->format('Y-m-d') }}
                @endslot
            @endcomponent

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">تعديل تقييم التحسن للمريض: {{ $patient->full_name }}</h4>
                            <p class="text-muted">التقييم بتاريخ: {{ $assessment->assessment_date_gregorian->format('d/m/Y') }} ({{$assessment->assessment_date_hijri}})</p>
                            <p class="text-muted">المقيم: {{ $assessment->assessor->name ?? 'غير محدد' }}</p>
                        </div>
                        <div class="card-body">
                            @if (session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif
                            <form action="{{ route('assessment.functional.update', ['patient' => $patient->id, 'assessment' => $assessment->id]) }}" method="POST">
                                @method('PUT')
                                @include('assessment.functional._form', ['assessment' => $assessment, 'currentResponses' => $currentResponses])
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @component('components.notification-box')
        @endcomponent
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        if($('.datetimepicker').length > 0) {
            $('.datetimepicker').datetimepicker({
                format: 'YYYY-MM-DD',
            });
        }
    });
</script>
@endpush