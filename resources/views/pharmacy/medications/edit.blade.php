@extends('layout.mainlayout')
@section('title', 'تعديل بيانات الدواء')

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        @component('components.page-header')
            @slot('title')
                الأدوية
            @endslot
            @slot('li_1')
                تعديل بيانات الدواء
            @endslot
        @endcomponent
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">تعديل: {{ $medication->name }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('pharmacy.medications.update', $medication->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label for="name" class="form-label">الاسم التجاري <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $medication->name) }}" required>
                                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="generic_name" class="form-label">الاسم العلمي</label>
                                        <input type="text" class="form-control @error('generic_name') is-invalid @enderror" id="generic_name" name="generic_name" value="{{ old('generic_name', $medication->generic_name) }}">
                                        @error('generic_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="manufacturer" class="form-label">الشركة المصنعة</label>
                                        <input type="text" class="form-control @error('manufacturer') is-invalid @enderror" id="manufacturer" name="manufacturer" value="{{ old('manufacturer', $medication->manufacturer) }}">
                                        @error('manufacturer') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="form" class="form-label">شكل الدواء</label>
                                        <input type="text" class="form-control @error('form') is-invalid @enderror" id="form" name="form" value="{{ old('form', $medication->form) }}">
                                        @error('form') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="strength" class="form-label">التركيز</label>
                                        <input type="text" class="form-control @error('strength') is-invalid @enderror" id="strength" name="strength" value="{{ old('strength', $medication->strength) }}">
                                        @error('strength') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label for="notes" class="form-label">ملاحظات</label>
                                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes', $medication->notes) }}</textarea>
                                        @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="text-end mt-4">
                                <button type="submit" class="btn btn-primary">تحديث البيانات</button>
                                <a href="{{ route('pharmacy.medications.index') }}" class="btn btn-secondary">إلغاء</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection