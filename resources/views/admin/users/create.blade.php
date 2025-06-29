@php $page = 'admin-users-create'; @endphp
@extends('layout.mainlayout')
@section('title', 'إضافة مستخدم جديد')

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        @component('components.page-header')
            @slot('title')
                إضافة مستخدم
            @endslot
            @slot('li_1')
                <a href="{{ route('admin.users.index') }}">المستخدمون</a>
            @endslot
            @slot('li_2')
                إضافة جديد
            @endslot
        @endcomponent

        <div class="row">
            <div class="col-lg-10 offset-lg-1">
                <div class="card">
                     <div class="card-header">
                        <h5 class="card-title">بيانات المستخدم الجديد</h5>
                    </div>
                    <div class="card-body">
                         <form action="{{ route('admin.users.store') }}" method="POST">
                            @include('admin.users._form')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection