@php $page = 'hr-shift-def-edit'; @endphp
@extends('layout.mainlayout')
@section('title', 'تعديل تعريف مناوبة')

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        @component('components.page-header')
            @slot('title')
                تعديل تعريف مناوبة
            @endslot
            @slot('li_1')
                 <a href="{{ route('hr.shift_definitions.index') }}">تعريفات المناوبات</a>
            @endslot
            @slot('li_2')
                تعديل
            @endslot
        @endcomponent
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="card">
                     <div class="card-header">
                        <h5 class="card-title">تعديل: {{ $shiftDefinition->name }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('hr.shift_definitions.update', $shiftDefinition->id) }}" method="POST">
                            @method('PUT')
                            @include('hr.shift_definitions._form')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection