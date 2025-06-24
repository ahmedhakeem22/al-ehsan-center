@php $page = 'bed-create'; @endphp
@extends('layout.mainlayout')
@section('title', 'إنشاء سرير جديد')

@section('content')
    <div class="page-wrapper">
        <div class="content">
            @component('components.page-header')
                @slot('title')
                    إدارة الأسرة
                @endslot
                @slot('li_1')
                    إنشاء سرير جديد
                @endslot
            @endcomponent

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">إضافة سرير جديد</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('occupancy.beds.store') }}" method="POST">
                                @include('occupancy.beds._form')
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @component('components.notification-box')
        @endcomponent
    </div>
@endsection