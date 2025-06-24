@php $page = 'admin-labtests-list'; @endphp
@extends('layout.mainlayout') {{-- أو القالب الرئيسي للإدارة لديك --}}
@section('title', 'قائمة الفحوصات المخبرية')

@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            @component('components.page-header')
                @slot('title')
                    إدارة الفحوصات المخبرية
                @endslot
                 @slot('li_1')
                    <a href="{{ route('admin.settings.index') }}">الإعدادات</a>
                @endslot
                @slot('li_2')
                    الفحوصات المخبرية
                @endslot
            @endcomponent

            <div class="row mb-3">
                <div class="col-md-12 text-end">
                    <a href="{{ route('admin.settings.labtests.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> إضافة فحص جديد
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
                                {{-- تم إزالة الكلاس "datatable" من هنا، وسنضيف ID لنهيئه يدويًا --}}
                                <table class="table table-hover table-center mb-0" id="labTestsDataTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>اسم الفحص</th>
                                            <th>الكود</th>
                                            <th>الوصف</th>
                                            <th>التكلفة</th>
                                            <th class="text-end">إجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($labTests as $labtest)
                                            <tr>
                                                <td>{{ ($labTests->currentPage() - 1) * $labTests->perPage() + $loop->iteration }}</td>
                                                <td>{{ $labtest->name }}</td>
                                                <td>{{ $labtest->code ?: '-' }}</td>
                                                <td>{{ Str::limit($labtest->description, 40) ?: '-' }}</td>
                                                <td>{{ $labtest->cost !== null ? number_format($labtest->cost, 2) : '-' }}</td>
                                                <td class="text-end">
                                                    <a href="{{ route('admin.settings.labtests.edit', $labtest->id) }}" class="btn btn-sm btn-outline-warning me-1" title="تعديل">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.settings.labtests.destroy', $labtest->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذا الفحص المخبري؟')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">لا توجد فحوصات مخبرية مضافة حاليًا.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                             <div class="mt-3">
                                {{ $labTests->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- @component('components.notification-box') @endcomponent --}} {{-- قم بإلغاء تعليق هذا إذا كنت تستخدمه --}}
    </div>
@endsection

@push('scripts')
{{-- تأكد من أن لديك مكتبة jQuery و DataTables مُضمنة في القالب الرئيسي --}}
{{-- مثال:
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
أو ما يعادلها محليًا
--}}
{{-- وأيضًا ملفات CSS الخاصة بـ DataTables في قسم @push('styles') أو في القالب الرئيسي --}}
{{-- مثال:
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
--}}
<script>
$(document).ready(function() {
    // تهيئة مخصصة لجدول الفحوصات المخبرية
    $('#labTestsDataTable').DataTable({
        "paging": false, // تعطيل ترقيم الصفحات من DataTables إذا كنت تستخدم ترقيم Laravel
        "searching": true, // تفعيل البحث من DataTables
        "info": false, // تعطيل معلومات "Showing x to y of z entries" إذا كنت تستخدم ترقيم Laravel
        "columnDefs": [
            { "orderable": false, "targets": [0, 5] } // تعطيل الفرز للعمود الأول (الترقيم) والعمود الأخير (الإجراءات)
        ],
        "language": {
            "decimal": "",
            "emptyTable": "لا توجد بيانات متاحة في الجدول",
            "info": "عرض _START_ إلى _END_ من إجمالي _TOTAL_ مدخلات",
            "infoEmpty": "عرض 0 إلى 0 من إجمالي 0 مدخلات",
            "infoFiltered": "(تمت تصفيته من إجمالي _MAX_ مدخلات)",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": "عرض _MENU_ مدخلات",
            "loadingRecords": "جارٍ التحميل...",
            "processing": "جارٍ المعالجة...",
            "search": "بحث:",
            "zeroRecords": "لم يتم العثور على سجلات مطابقة",
            "paginate": {
                "first": "الأول",
                "last": "الأخير",
                "next": "التالي",
                "previous": "السابق"
            },
            "aria": {
                "sortAscending": ": تفعيل لفرز العمود تصاعديًا",
                "sortDescending": ": تفعيل لفرز العمود تنازليًا"
            }
        }
        // يمكنك إضافة المزيد من خيارات DataTables هنا حسب الحاجة
    });
});
</script>
@endpush