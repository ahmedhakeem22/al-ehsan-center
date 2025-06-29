@php $page = 'hr-shift-def-create'; @endphp
@extends('layout.mainlayout')
@section('title', 'إضافة تعريف مناوبة')

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        @component('components.page-header')
            @slot('title')
                إضافة تعريف مناوبة
            @endslot
            @slot('li_1')
                <a href="{{ route('hr.shift_definitions.index') }}">تعريفات المناوبات</a>
            @endslot
            @slot('li_2')
                إضافة جديد
            @endslot
        @endcomponent

        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="card">
                     <div class="card-header">
                        <h5 class="card-title">مناوبة جديدة</h5>
                    </div>
                    <div class="card-body">
                         <form action="{{ route('hr.shift_definitions.store') }}" method="POST">
                            @include('hr.shift_definitions._form')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection