@extends('layout.mainlayout')
@section('title', 'إضافة دواء جديد')

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        @component('components.page-header')
            @slot('title')
                الأدوية
            @endslot
            @slot('li_1')
                إضافة دواء جديد
            @endslot
        @endcomponent
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">بيانات الدواء الجديد</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('pharmacy.medications.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label for="name" class="form-label">الاسم التجاري <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="generic_name" class="form-label">الاسم العلمي</label>
                                        <input type="text" class="form-control @error('generic_name') is-invalid @enderror" id="generic_name" name="generic_name" value="{{ old('generic_name') }}">
                                        @error('generic_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="manufacturer" class="form-label">الشركة المصنعة</label>
                                        <input type="text" class="form-control @error('manufacturer') is-invalid @enderror" id="manufacturer" name="manufacturer" value="{{ old('manufacturer') }}">
                                         @error('manufacturer') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="form" class="form-label"> الشكل الدوائي (e.g., Tablet, Syrup)</label>
                                        <input type="text" class="form-control @error('form') is-invalid @enderror" id="form" name="form" value="{{ old('form') }}">
                                        @error('form') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="strength" class="form-label">التركيز (e.g., 50mg, 10mg/5ml)</label>
                                        <input type="text" class="form-control @error('strength') is-invalid @enderror" id="strength" name="strength" value="{{ old('strength') }}">
                                        @error('strength') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label for="notes" class="form-label">ملاحظات</label>
                                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                                        @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="text-end mt-4">
                                <button type="submit" class="btn btn-primary">حفظ الدواء</button>
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