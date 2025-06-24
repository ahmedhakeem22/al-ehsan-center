@php $page = 'assign-bed'; @endphp
@extends('layout.mainlayout')
@section('title', 'تسكين المريض: ' . $patient->full_name)

@section('content')
    <div class="page-wrapper">
        <div class="content">
            @component('components.page-header')
                @slot('title')
                    إدارة الإشغال
                @endslot
                @slot('li_1')
                    تسكين المريض: {{ $patient->full_name }} ({{ $patient->file_number }})
                @endslot
            @endcomponent

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">اختر سريراً للمريض: {{ $patient->full_name }}</h4>
                            @if(session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif
<form action="{{ route('patient_management.admissions.assign_bed', $patient->id) }}" method="POST">                                @csrf
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group local-forms">
                                            <label for="bed_id">السرير المتاح <span class="login-danger">*</span></label>
                                            <select name="bed_id" id="bed_id" class="form-select @error('bed_id') is-invalid @enderror" required>
                                                <option value="">-- اختر سريراً --</option>
                                                @forelse ($floors as $floor)
                                                    <optgroup label="طابق: {{ $floor->name }}">
                                                        @foreach ($floor->rooms as $room)
                                                            @if($room->beds->where('status', 'vacant')->count() > 0)
                                                                <optgroup label="    غرفة: {{ $room->room_number }} (السعة: {{ $room->capacity }})">
                                                                    @foreach ($room->beds->where('status', 'vacant')->sortBy('bed_number') as $bed)
                                                                        <option value="{{ $bed->id }}" {{ old('bed_id') == $bed->id ? 'selected' : '' }}>
                                                                                    سرير رقم: {{ $bed->bed_number }}
                                                                        </option>
                                                                    @endforeach
                                                                </optgroup>
                                                            @endif
                                                        @endforeach
                                                    </optgroup>
                                                @empty
                                                    <option value="" disabled>لا توجد طوابق أو أسرة متاحة حالياً.</option>
                                                @endforelse
                                            </select>
                                            @error('bed_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4 align-self-end">
                                        <div class="form-group">
                                             <button type="submit" class="btn btn-primary submit-form w-100">تسكين المريض</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                   <a href="{{ route('patient_management.patients.show', $patient->id) }}" class="btn btn-secondary">العودة لملف المريض</a>
                               </div>
                            </form>
                            @if($floors->isEmpty() || $floors->sum(function($floor) { return $floor->rooms->sum(function($room){ return $room->beds->where('status', 'vacant')->count(); }); }) == 0)
                                <div class="alert alert-warning mt-3">
                                    <strong>تنبيه:</strong> لا توجد أسرة شاغرة حالياً في المستشفى. يرجى مراجعة <a href="{{-- route('occupancy.dashboard') --}}">لوحة تحكم الإشغال</a> أو إضافة أسرة جديدة.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @component('components.notification-box')
        @endcomponent
    </div>
@endsection