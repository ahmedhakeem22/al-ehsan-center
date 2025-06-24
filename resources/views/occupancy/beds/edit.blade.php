@php $page = 'bed-edit'; @endphp
@extends('layout.mainlayout')
@section('title', 'تعديل السرير: ' . $bed->bed_number)

@section('content')
    <div class="page-wrapper">
        <div class="content">
            @component('components.page-header')
                @slot('title')
                    إدارة الأسرة
                @endslot
                @slot('li_1')
                    تعديل السرير: {{ $bed->bed_number }} ({{ $bed->room->floor->name }} - غرفة {{ $bed->room->room_number }})
                @endslot
            @endcomponent

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">تعديل بيانات السرير</h4>
                        </div>
                        <div class="card-body">
                             @if ($bed->status === 'occupied' && $bed->patient)
                                <div class="alert alert-info">
                                    هذا السرير مشغول حالياً بواسطة المريض:
                                    <a href="{{ route('patient_management.patients.show', $bed->patient->id) }}">{{ $bed->patient->full_name }} ({{ $bed->patient->file_number }})</a>.
                                    <br>تغيير حالة السرير إلى غير "مشغول" سيؤدي إلى إلغاء تسكين المريض من هذا السرير.
                                </div>
                            @endif
                            <form action="{{ route('occupancy.beds.update', $bed->id) }}" method="POST">
                                @method('PUT')
                                @include('occupancy.beds._form', ['bed' => $bed, 'bedStatuses' => $bedStatuses])
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