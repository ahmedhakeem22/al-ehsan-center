@extends('layout.mainlayout')
@section('title', 'الفحوصات المخبرية المتاحة')

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">

        @component('components.page-header')
            @slot('title') المختبر @endslot
            @slot('li_1') قائمة الفحوصات المتاحة @endslot
        @endcomponent

        <div class="row mb-4">
            <div class="col-md-12 text-end">
                <a href="{{ route('lab.available_tests.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> إضافة فحص جديد
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title"><i class="fas fa-filter me-2"></i> فلترة البحث</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('lab.available_tests.index') }}" method="GET">
                    <div class="row align-items-end">
                        <div class="col-md-9">
                            <label for="search" class="form-label">بحث</label>
                            <input type="text" id="search" name="search" class="form-control" placeholder="بحث بالاسم أو الرمز..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3 d-flex">
                            <button type="submit" class="btn btn-primary flex-grow-1 me-2"><i class="fas fa-search"></i> بحث</button>
                            <a href="{{ route('lab.available_tests.index') }}" class="btn btn-outline-secondary flex-grow-1"><i class="fas fa-redo"></i></a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-sm-12">
                <div class="card card-table">
                    <div class="card-header">
                        <h5 class="card-title">قائمة الفحوصات ({{ $labTests->total() }})</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-center mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>اسم الفحص</th>
                                        <th>الرمز</th>
                                        <th>المدى المرجعي</th>
                                        <th>التكلفة</th>
                                        <th class="text-end">إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($labTests as $test)
                                    <tr>
                                        <td>{{ $labTests->firstItem() + $loop->index }}</td>
                                        <td>{{ $test->name }}</td>
                                        <td>{{ $test->code ?? '-' }}</td>
                                        <td>{{ $test->reference_range ?? '-' }}</td>
                                        <td>{{ number_format($test->cost, 2) }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('lab.available_tests.edit', $test->id) }}" class="btn btn-sm btn-outline-warning me-1" title="تعديل"><i class="fas fa-edit"></i></a>
                                            <form action="{{ route('lab.available_tests.destroy', $test->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الفحص؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="6" class="text-center py-4"><h5>لا توجد فحوصات لعرضها</h5></td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if ($labTests->hasPages())
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $labTests->appends(request()->query())->links('pagination::bootstrap-5') }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection