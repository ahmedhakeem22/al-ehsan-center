@php $page = 'hr-documents-create'; @endphp
@extends('layout.mainlayout')
@section('title', 'إضافة مستند للموظف ' . $employee->full_name)

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        @component('components.page-header')
            @slot('title')
                إضافة مستند
            @endslot
            @slot('li_1')
                <a href="{{ route('hr.employees.show', $employee->id) }}">{{ $employee->full_name }}</a>
            @endslot
            @slot('li_2')
                إضافة مستند
            @endslot
        @endcomponent

        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="card">
                     <div class="card-header">
                        <h5 class="card-title">رفع مستند جديد للموظف: {{ $employee->full_name }}</h5>
                    </div>
                    <div class="card-body">
                         <form action="{{ route('hr.documents.store', $employee->id) }}" method="POST" enctype="multipart/form-data">
                            @include('hr.documents._form')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection