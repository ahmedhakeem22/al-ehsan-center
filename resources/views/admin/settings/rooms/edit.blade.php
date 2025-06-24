@php $page = 'admin-room-edit'; @endphp
@extends('layout.mainlayout')
@section('title', 'تعديل الغرفة: ' . $room->room_number)

@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            @component('components.page-header')
                @slot('title')
                    إدارة الغرف
                @endslot
                @slot('li_1')
                     <a href="{{ route('admin.settings.index') }}">الإعدادات</a>
                @endslot
                @slot('li_2')
                    <a href="{{ route('admin.settings.rooms.index') }}">الغرف</a>
                @endslot
                @slot('li_3')
                    تعديل الغرفة: {{ $room->room_number }}
                @endslot
            @endcomponent

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">تعديل بيانات الغرفة</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.settings.rooms.update', $room->id) }}" method="POST">
                                @method('PUT')
                                @include('admin.settings.rooms._form', ['room' => $room, 'floors' => $floors])
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection