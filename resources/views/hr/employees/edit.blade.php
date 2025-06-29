@php $page = 'hr-employees-edit'; @endphp
@extends('layout.mainlayout')
@section('title', 'تعديل بيانات الموظف')

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        @component('components.page-header')
            @slot('title')
                تعديل موظف
            @endslot
            @slot('li_1')
                <a href="{{ route('hr.employees.index') }}">الموظفين</a>
            @endslot
            @slot('li_2')
                تعديل
            @endslot
        @endcomponent

        <div class="row">
            <div class="col-sm-12">
                <form action="{{ route('hr.employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
                    @method('PUT')
                    @include('hr.employees._form', ['employee' => $employee])
                </form>
            </div>
        </div>
    </div>
</div>
@endsection