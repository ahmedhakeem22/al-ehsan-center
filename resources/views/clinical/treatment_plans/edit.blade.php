@extends('layout.mainlayout')
@section('title', 'تعديل الخطة العلاجية #' . $plan->id)

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">

        @component('components.page-header')
            @slot('title') الخطط العلاجية @endslot
            @slot('li_1') <a href="{{ route('clinical.treatment_plans.index', $patient->id) }}">قائمة الخطط</a> @endslot
            @slot('li_2') تعديل الخطة رقم #{{ $plan->id }} @endslot
        @endcomponent

        <div class="row">
            <div class="col-md-10 offset-md-1">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">تعديل بيانات الخطة العلاجية</h5>
                    </div>
                    <form action="{{ route('clinical.treatment_plans.update', [$patient->id, $plan->id]) }}" method="POST">
                        @method('PUT')
                        <div class="card-body">
                            @include('clinical.treatment_plans._form')
                        </div>
                        <div class="card-footer text-end">
                            <a href="{{ route('clinical.treatment_plans.show', [$patient->id, $plan->id]) }}" class="btn btn-secondary">إلغاء</a>
                            <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection