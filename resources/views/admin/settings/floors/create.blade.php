@php $page = 'admin-floor-create'; @endphp
@extends('layout.mainlayout')
@section('title', 'إنشاء طابق جديد')

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
                    إنشاء طابق جديد
                @endslot
            @endcomponent

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">إضافة طابق جديد</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.settings.floors.store') }}" method="POST">
                                @include('admin.settings.floors._form')
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection