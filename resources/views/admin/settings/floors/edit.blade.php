@php $page = 'admin-floor-edit'; @endphp
@extends('layout.mainlayout')
@section('title', 'تعديل الطابق: ' . $floor->name)

@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            @component('components.page-header')
                @slot('title')
                    إدارة الطوابق
                @endslot
                @slot('li_1')
                     <a href="{{ route('admin.settings.index') }}">الإعدادات</a>
                @endslot
                @slot('li_2')
                    <a href="{{ route('admin.settings.floors.index') }}">الطوابق</a>
                @endslot
                @slot('li_3')
                    تعديل الطابق: {{ $floor->name }}
                @endslot
            @endcomponent

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">تعديل بيانات الطابق</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.settings.floors.update', $floor->id) }}" method="POST">
                                @method('PUT')
                                @include('admin.settings.floors._form', ['floor' => $floor])
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection