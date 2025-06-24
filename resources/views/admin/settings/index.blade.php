@php $page = 'admin-settings'; @endphp
@extends('layout.mainlayout') {{-- أو القالب الرئيسي للإدارة --}}
@section('title', 'إعدادات النظام')

@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            @component('components.page-header')
                @slot('title')
                    إعدادات النظام
                @endslot
                @slot('li_1')
                    لوحة التحكم
                @endslot
                @slot('li_2')
                    الإعدادات
                @endslot
            @endcomponent

            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-building fa-3x mb-3 text-primary"></i>
                            <h5 class="card-title">إدارة الطوابق</h5>
                            <p class="card-text">إضافة، تعديل، وحذف طوابق المستشفى.</p>
                            <a href="{{ route('admin.settings.floors.index') }}" class="btn btn-primary">الذهاب إلى الطوابق</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-door-open fa-3x mb-3 text-success"></i>
                            <h5 class="card-title">إدارة الغرف</h5>
                            <p class="card-text">إضافة، تعديل، وحذف غرف المرضى.</p>
                            <a href="{{ route('admin.settings.rooms.index') }}" class="btn btn-success">الذهاب إلى الغرف</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-flask fa-3x mb-3 text-info"></i>
                            <h5 class="card-title">إدارة الفحوصات المخبرية</h5>
                            <p class="card-text">إضافة، تعديل، وحذف الفحوصات المتاحة.</p>
                            <a href="{{ route('admin.settings.labtests.index') }}" class="btn btn-info">الذهاب إلى الفحوصات</a>
                        </div>
                    </div>
                </div>
            </div>
            {{-- يمكنك إضافة المزيد من بطاقات الإعدادات هنا --}}
        </div>
    </div>
@endsection