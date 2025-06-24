@php $page = 'patient-media'; @endphp
@extends('layout.mainlayout')
@section('title', 'وسائط المريض: ' . $patient->full_name)

@section('content')
    <div class="page-wrapper">
        <div class="content">
            @component('components.page-header')
                @slot('title')
                    إدارة وسائط المريض
                @endslot
                @slot('li_1')
                    <a href="{{ route('patient_management.patients.show', $patient->id) }}">{{ $patient->full_name }} ({{$patient->file_number}})</a> - معرض الوسائط
                @endslot
            @endcomponent

            <div class="row mb-3">
                <div class="col-md-6">
                    <h4>معرض وسائط المريض: {{ $patient->full_name }}</h4>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('patient_management.media.create', $patient->id) }}" class="btn btn-primary">
                        <i class="fa fa-plus"></i> إضافة وسائط جديدة
                    </a>
                </div>
            </div>

            @include('patient_management.media._media_list_partial', ['mediaItems' => $mediaItems, 'show_pagination' => true])

            @if($mediaItems->isEmpty())
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body text-center">
                            <p>لا توجد وسائط لعرضها لهذا المريض حالياً.</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>
        @component('components.notification-box')
        @endcomponent
    </div>
@endsection