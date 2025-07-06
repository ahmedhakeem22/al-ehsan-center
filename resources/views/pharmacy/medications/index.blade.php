@extends('layout.mainlayout')
@section('title', 'إدارة الأدوية')

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">

        @component('components.page-header')
            @slot('title')
                الصيدلية
            @endslot
            @slot('li_1')
                قائمة الأدوية
            @endslot
        @endcomponent

        <div class="row mb-4">
            <div class="col-md-12 text-end">
                <a href="{{ route('pharmacy.medications.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> إضافة دواء جديد
                </a>
            </div>
        </div>

        {{-- بطاقة الفلترة --}}
        <div class="card">
            <div class="card-header">
                <h5 class="card-title"><i class="fas fa-filter me-2"></i> فلترة البحث</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('pharmacy.medications.index') }}" method="GET">
                    <div class="row align-items-end">
                        <div class="col-md-5">
                            <label for="search" class="form-label">بحث</label>
                            <input type="text" id="search" name="search" class="form-control" placeholder="بحث بالاسم التجاري, الاسم العلمي, الشركة..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="form" class="form-label">شكل الدواء</label>
                            <select name="form" id="form" class="form-select">
                                <option value="">جميع الأشكال</option>
                                @foreach($forms as $form)
                                    <option value="{{ $form }}" @selected(request('form') == $form)>{{ $form }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 d-flex">
                            <button type="submit" class="btn btn-primary flex-grow-1 me-2"><i class="fas fa-search"></i> بحث</button>
                            <a href="{{ route('pharmacy.medications.index') }}" class="btn btn-outline-secondary flex-grow-1"><i class="fas fa-redo"></i> إعادة</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- بطاقة النتائج --}}
        <div class="row mt-4">
            <div class="col-sm-12">
                <div class="card card-table">
                    <div class="card-header">
                        <h5 class="card-title">قائمة الأدوية ({{ $medications->total() }})</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-center mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>الاسم التجاري</th>
                                        <th>الاسم العلمي</th>
                                        <th>الشركة المصنعة</th>
                                        <th>الشكل الدوائي</th>
                                        <th>التركيز</th>
                                        <th class="text-end">إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($medications as $medication)
                                    <tr>
                                        <td>{{ $medications->firstItem() + $loop->index }}</td>
                                        <td>{{ $medication->name }}</td>
                                        <td>{{ $medication->generic_name ?? '-' }}</td>
                                        <td>{{ $medication->manufacturer ?? '-' }}</td>
                                        <td>{{ $medication->form ?? '-' }}</td>
                                        <td>{{ $medication->strength ?? '-' }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('pharmacy.medications.edit', $medication->id) }}" class="btn btn-sm btn-outline-warning me-1" title="تعديل"><i class="fas fa-edit"></i></a>
                                            <form action="{{ route('pharmacy.medications.destroy', $medication->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الدواء؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4"><h5>لا توجد أدوية تطابق بحثك</h5></td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if ($medications->hasPages())
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $medications->appends(request()->query())->links('pagination::bootstrap-5') }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection