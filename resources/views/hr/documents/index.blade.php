@php $page = 'hr-documents-list'; @endphp
@extends('layout.mainlayout')
@section('title', 'مستندات الموظف ' . $employee->full_name)

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        @component('components.page-header')
            @slot('title')
                مستندات الموظف
            @endslot
            @slot('li_1')
                <a href="{{ route('hr.employees.show', $employee->id) }}">{{ $employee->full_name }}</a>
            @endslot
            @slot('li_2')
                قائمة المستندات
            @endslot
        @endcomponent

        <div class="row mb-3">
            <div class="col-md-12 text-end">
                <a href="{{ route('hr.documents.create', $employee->id) }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> إضافة مستند جديد
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card card-table">
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                         @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif
                        <div class="table-responsive">
                            <table class="table table-hover table-center mb-0">
                                <thead>
                                    <tr>
                                        <th>نوع المستند</th>
                                        <th>اسم الملف</th>
                                        <th>تاريخ الرفع</th>
                                        <th>رُفع بواسطة</th>
                                        <th class="text-end">إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($documents as $doc)
                                    <tr>
                                        <td>{{ $doc->document_type }}</td>
                                        <td>{{ Str::limit($doc->file_name, 40) }}</td>
                                        <td>{{ $doc->uploaded_at->format('Y-m-d h:i A') }}</td>
                                        <td>{{ $doc->uploader->name ?? 'غير معروف' }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('hr.documents.download', [$employee->id, $doc->id]) }}" class="btn btn-sm btn-outline-success me-1">
                                                <i class="fas fa-download"></i> تحميل
                                            </a>
                                            <form action="{{ route('hr.documents.destroy', [$employee->id, $doc->id]) }}" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذا المستند؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i> حذف
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center">لا توجد مستندات مضافة لهذا الموظف.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                         <div class="mt-3">
                            {{ $documents->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection