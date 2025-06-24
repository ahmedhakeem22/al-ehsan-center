@php $page = 'patients'; @endphp
@extends('layout.mainlayout')
@section('title', 'قائمة المرضى')

@section('content')
    <div class="page-wrapper">
        <div class="content">

            @component('components.page-header')
                @slot('title')
                    المرضى
                @endslot
                @slot('li_1')
                    قائمة المرضى
                @endslot
            @endcomponent

            <div class="row mb-3">
                <div class="col-md-12">
                    <form action="{{ route('patient_management.patients.index') }}" method="GET">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="بحث بالاسم, رقم الملف, المحافظة..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-select">
                                    <option value="">كل الحالات</option>
                                    @foreach($statuses as $key => $value)
                                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">بحث</button>
                            </div>
                             <div class="col-md-3 text-end">
                                <a href="{{ route('patient_management.admissions.register') }}" class="btn btn-success">
                                    <i class="fa fa-plus"></i> تسجيل مريض جديد وتسكينه
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card card-table show-entire">
                        <div class="card-body">
                            <div class="table-responsive">
    {{-- قم بإزالة الكلاس datatable مؤقتًا إذا كان موجودًا لاختبار --}}
    <table class="table border-0 custom-table comman-table mb-0" id="patientsTable"> {{-- أضفت id هنا --}}
        <thead>
            <tr>
                <th>#</th>
                <th>الصورة</th>
                <th>رقم الملف</th>
                <th>الاسم الكامل</th>
                <th>العمر التقريبي</th>
                <th>المحافظة</th>
                <th>تاريخ الوصول</th>
                <th>الحالة</th>
                <th>السرير الحالي</th>
                <th class="text-end">إجراءات</th>
            </tr>
        </thead>
                                    <tbody>
                                        @forelse ($patients as $patient)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <a href="{{ route('patient_management.patients.show', $patient->id) }}">
                                                        <img width="35" height="35"
                                                             src="{{ $patient->profile_image_path ? Storage::url($patient->profile_image_path) : asset('assets/img/placeholder-user.png') }}"
                                                             class="rounded-circle m-r-5" alt="{{ $patient->full_name }}">
                                                    </a>
                                                </td>
                                                <td>{{ $patient->file_number }}</td>
                                                <td>
                                                    <a href="{{ route('patient_management.patients.show', $patient->id) }}">{{ $patient->full_name }}</a>
                                                </td>
                                                <td>{{ $patient->approximate_age ?: 'غير محدد' }}</td>
                                                <td>{{ $patient->province ?: 'غير محدد' }}</td>
                                                <td>{{ $patient->arrival_date ? \Carbon\Carbon::parse($patient->arrival_date)->format('Y-m-d') : 'غير محدد' }}</td>
                                                <td>
                                                    @if($patient->status == 'active')
                                                        <span class="badge bg-success-light">نشط</span>
                                                    @elseif($patient->status == 'discharged')
                                                        <span class="badge bg-warning-light">خروج</span>
                                                    @elseif($patient->status == 'deceased')
                                                        <span class="badge bg-danger-light">متوفى</span>
                                                    @elseif($patient->status == 'transferred')
                                                        <span class="badge bg-info-light">محول</span>
                                                    @else
                                                        {{ $patient->status }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($patient->currentBed)
                                                        {{ $patient->currentBed->bed_number }} ({{ $patient->currentBed->room->room_number }} - {{ $patient->currentBed->room->floor->name }})
                                                    @else
                                                        <span class="text-muted">غير مسكن</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <div class="dropdown dropdown-action">
                                                        <a href="javascript:;" class="action-icon dropdown-toggle"
                                                            data-bs-toggle="dropdown" aria-expanded="false"><i
                                                                class="fa fa-ellipsis-v"></i></a>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <a class="dropdown-item" href="{{ route('patient_management.patients.show', $patient->id) }}"><i
                                                                    class="fa-solid fa-eye m-r-5"></i> عرض</a>
                                                            <a class="dropdown-item" href="{{ route('patient_management.patients.edit', $patient->id) }}"><i
                                                                    class="fa-solid fa-pen-to-square m-r-5"></i> تعديل</a>
                                                            <form action="{{ route('patient_management.patients.destroy', $patient->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذا المريض؟ سيتم حذف جميع بياناته المتعلقة.');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item"><i
                                                                    class="fa fa-trash-alt m-r-5"></i> حذف</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center">لا توجد بيانات مرضى لعرضها.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                {{ $patients->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @component('components.notification-box')
        @endcomponent
    </div>
@endsection
@push('scripts')
<script>
    $(document).ready(function() {
        $('#patientsTable').DataTable({
            // يمكنك إضافة خيارات هنا إذا لزم الأمر، مثل تعطيل الفرز لأعمدة معينة
             "columnDefs": [
                 { "orderable": false, "targets": [1, 9] } // مثال: تعطيل الفرز لعمود الصورة والإجراءات
             ],
             "language": { // إضافة ترجمة بسيطة للواجهة إذا أردت
                 "search": "بحث:",
                 "lengthMenu": "عرض _MENU_ سجلات",
                 "info": "عرض _START_ إلى _END_ من إجمالي _TOTAL_ سجلات",
                 "infoEmpty": "لا توجد سجلات متاحة",
                 "infoFiltered": "(تمت تصفيته من إجمالي _MAX_ سجلات)",
                 "paginate": {
                     "first": "الأول",
                     "last": "الأخير",
                    "next": "التالي",
                     "previous": "السابق"
                 },
                 "zeroRecords": "لم يتم العثور على سجلات مطابقة",
                 "emptyTable": "لا توجد بيانات متاحة في الجدول"
             }
        });
    });
</script>
@endpush