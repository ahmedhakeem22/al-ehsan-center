@php $page = 'admin-labtest-create'; @endphp
@extends('layout.mainlayout')
@section('title', 'إنشاء فحص مخبري جديد')

@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            @component('components.page-header')
                @slot('title')
                    إدارة الفحوصات المخبرية
                @endslot
                 @slot('li_1')
                    <a href="{{ route('admin.settings.index') }}">الإعدادات</a>
                @endslot
                @slot('li_2')
                    <a href="{{ route('admin.settings.labtests.index') }}">الفحوصات</a>
                @endslot
                @slot('li_3')
                    إنشاء فحص جديد
                @endslot
            @endcomponent

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">إضافة فحص مخبري جديد</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.settings.labtests.store') }}" method="POST">
                                @include('admin.settings.labtests._form')
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection