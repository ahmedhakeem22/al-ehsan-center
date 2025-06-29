@php $page = 'hr-employees-create'; @endphp
@extends('layout.mainlayout')
@section('title', 'إضافة موظف جديد')

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        @component('components.page-header')
            @slot('title')
                إضافة موظف
            @endslot
            @slot('li_1')
                <a href="{{ route('hr.employees.index') }}">الموظفين</a>
            @endslot
            @slot('li_2')
                إضافة جديد
            @endslot
        @endcomponent

        <div class="row">
            <div class="col-sm-12">
                <form action="{{ route('hr.employees.store') }}" method="POST" enctype="multipart/form-data">
                    @include('hr.employees._form')
                </form>
            </div>
        </div>
    </div>
</div>
@endsection