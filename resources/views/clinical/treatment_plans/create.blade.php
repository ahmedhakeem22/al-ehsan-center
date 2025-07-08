@extends('layout.mainlayout')
@section('title', 'إنشاء خطة علاجية جديدة')

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">

        @component('components.page-header')
            @slot('title') الخطط العلاجية @endslot
            @slot('li_1') <a href="{{ route('clinical.treatment_plans.index', $patient->id) }}">قائمة الخطط</a> @endslot
            @slot('li_2') إنشاء خطة جديدة لـ: {{ $patient->full_name }} @endslot
        @endcomponent

        <div class="row">
            <div class="col-md-10 offset-md-1">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">بيانات الخطة العلاجية الجديدة</h5>
                    </div>
                    <form action="{{ route('clinical.treatment_plans.store', $patient->id) }}" method="POST">
                        <div class="card-body">
                            @include('clinical.treatment_plans._form')
                        </div>
                        <div class="card-footer text-end">
                            <a href="{{ route('clinical.treatment_plans.index', $patient->id) }}" class="btn btn-secondary">إلغاء</a>
                            <button type="submit" class="btn btn-primary">حفظ الخطة</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection