@php $page = 'add-patient-media'; @endphp
@extends('layout.mainlayout')
@section('title', 'إضافة وسائط للمريض: ' . $patient->full_name)

@section('content')
    <div class="page-wrapper">
        <div class="content">
            @component('components.page-header')
                @slot('title')
                    إدارة وسائط المريض
                @endslot
                @slot('li_1')
                     <a href="{{ route('patient_management.patients.show', $patient->id) }}">{{ $patient->full_name }} ({{$patient->file_number}})</a> - إضافة وسائط جديدة
                @endslot
            @endcomponent

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">إضافة وسائط جديدة للمريض: {{ $patient->full_name }}</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('patient_management.media.store', $patient->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                {{-- حقل رفع الصور --}}
                                <div class="form-group row mb-4">
                                    <label class="col-form-label col-md-2">ملفات الصور</label>
                                    <div class="col-md-10">
                                        <input type="file" class="form-control @error('image_files') is-invalid @enderror @error('image_files.*') is-invalid @enderror" name="image_files[]" multiple accept="image/jpeg,image/png,image/gif,image/jpg">
                                        @error('image_files')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        @foreach ($errors->get('image_files.*') as $message)
                                            @foreach ($message as $error)
                                                <div class="invalid-feedback d-block">{{ $error }}</div>
                                            @endforeach
                                        @endforeach
                                        <small class="form-text text-muted">يمكنك اختيار عدة صور. الصيغ المسموحة: jpeg, png, jpg, gif. الحجم الأقصى لكل ملف: 20MB.</small>
                                    </div>
                                </div>

                                {{-- حقل رفع الفيديوهات --}}
                                <div class="form-group row mb-4">
                                    <label class="col-form-label col-md-2">ملفات الفيديو</label>
                                    <div class="col-md-10">
                                        <input type="file" class="form-control @error('video_files') is-invalid @enderror @error('video_files.*') is-invalid @enderror" name="video_files[]" multiple accept="video/mp4,video/quicktime,video/avi,video/mpeg">
                                        @error('video_files')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        @foreach ($errors->get('video_files.*') as $message)
                                            @foreach ($message as $error)
                                                <div class="invalid-feedback d-block">{{ $error }}</div>
                                            @endforeach
                                        @endforeach
                                        <small class="form-text text-muted">يمكنك اختيار عدة فيديوهات. الصيغ المسموحة: mp4, mov, avi, mpeg. الحجم الأقصى لكل ملف: 20MB (قد يكون أعلى للفيديو حسب إعدادات الخادم).</small>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-md-2">وصف عام للوسائط (اختياري)</label>
                                    <div class="col-md-10">
                                        <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="3" placeholder="هذا الوصف سيطبق على جميع الملفات المرفوعة في هذه المرة">{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">رفع الوسائط</button>
                                    <a href="{{ route('patient_management.media.index', $patient->id) }}" class="btn btn-secondary">إلغاء</a>
                                </div>
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