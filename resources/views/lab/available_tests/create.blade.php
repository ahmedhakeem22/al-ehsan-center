@extends('layout.mainlayout')
@section('title', 'إضافة فحص مخبري جديد')

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        @component('components.page-header')
            @slot('title') الفحوصات المتاحة @endslot
            @slot('li_1') إضافة فحص جديد @endslot
        @endcomponent
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header"><h5 class="card-title">بيانات الفحص الجديد</h5></div>
                    <div class="card-body">
                        <form action="{{ route('lab.available_tests.store') }}" method="POST">
                            @include('lab.available_tests._form')
                            <div class="text-end mt-4">
                                <button type="submit" class="btn btn-primary">حفظ الفحص</button>
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