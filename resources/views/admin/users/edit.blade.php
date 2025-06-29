@php $page = 'admin-users-edit'; @endphp
@extends('layout.mainlayout')
@section('title', 'تعديل بيانات المستخدم')

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        @component('components.page-header')
            @slot('title')
                تعديل مستخدم
            @endslot
            @slot('li_1')
                <a href="{{ route('admin.users.index') }}">المستخدمون</a>
            @endslot
            @slot('li_2')
                تعديل
            @endslot
        @endcomponent

        <div class="row">
            <div class="col-lg-10 offset-lg-1">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">تعديل بيانات: {{ $user->name }}</h5>
                    </div>
                    <div class="card-body">
                         <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                            @method('PUT')
                            @include('admin.users._form', ['user' => $user])
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection