@extends('layout.mainlayout')
@section('title', 'تعديل ملاحظة سريرية #' . $note->id)

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">

        @component('components.page-header')
            @slot('title') الملاحظات السريرية @endslot
            @slot('li_1') <a href="{{ route('clinical.notes.index', $patient->id) }}">قائمة الملاحظات</a> @endslot
            @slot('li_2') تعديل الملاحظة رقم #{{ $note->id }} @endslot
        @endcomponent

        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                     <div class="card-header">
                        <h5 class="card-title">تعديل بيانات الملاحظة</h5>
                    </div>
                    <form action="{{ route('clinical.notes.update', [$patient->id, $note->id]) }}" method="POST">
                        @method('PUT')
                        <div class="card-body">
                            @include('clinical.notes._form')
                        </div>
                        <div class="card-footer text-end">
                            <a href="{{ route('clinical.notes.show', [$patient->id, $note->id]) }}" class="btn btn-secondary">إلغاء</a>
                            <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection