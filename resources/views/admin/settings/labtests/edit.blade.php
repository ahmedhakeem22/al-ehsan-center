@php $page = 'admin-labtest-edit'; @endphp
@extends('layout.mainlayout')
@section('title', 'تعديل الفحص: ' . $labtest->name)

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
                    تعديل الفحص: {{ $labtest->name }}
                @endslot
            @endcomponent

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">تعديل بيانات الفحص المخبري</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.settings.labtests.update', $labtest->id) }}" method="POST">
                                @method('PUT')
                                @include('admin.settings.labtests._form', ['labtest' => $labtest])
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection