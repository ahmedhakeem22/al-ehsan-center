@extends('layout.mainlayout')
@section('title', 'تعديل فحص مخبري')

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        @component('components.page-header')
            @slot('title') الفحوصات المتاحة @endslot
            @slot('li_1') تعديل فحص @endslot
        @endcomponent
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header"><h5 class="card-title">تعديل: {{ $availableLabTest->name }}</h5></div>
                    <div class="card-body">
                        <form action="{{ route('lab.available_tests.update', $availableLabTest->id) }}" method="POST">
                            @method('PUT')
                            @include('lab.available_tests._form', ['availableLabTest' => $availableLabTest])
                            <div class="text-end mt-4">
                                <button type="submit" class="btn btn-primary">تحديث البيانات</button>
                                <a href="{{ route('lab.available_tests.index') }}" class="btn btn-secondary">إلغاء</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection