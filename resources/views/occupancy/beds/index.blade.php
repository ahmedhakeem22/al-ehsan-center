@php $page = 'beds-list'; @endphp
@extends('layout.mainlayout')
@section('title', 'قائمة الأسرة')

@section('content')
    <div class="page-wrapper">
        <div class="content">
            @component('components.page-header')
                @slot('title')
                    إدارة الأسرة
                @endslot
                @slot('li_1')
                    قائمة الأسرة
                @endslot
            @endcomponent

            <div class="row mb-3">
                <div class="col-md-12">
                    <form action="{{ route('occupancy.beds.index') }}" method="GET" class="filter-form">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label for="floor_id_filter" class="form-label">الطابق</label>
                                <select name="floor_id" id="floor_id_filter" class="form-select">
                                    <option value="">كل الطوابق</option>
                                    @foreach ($floors as $id => $name)
                                        <option value="{{ $id }}" {{ request('floor_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="room_id_filter" class="form-label">الغرفة</label>
                                <select name="room_id" id="room_id_filter" class="form-select">
                                    <option value="">كل الغرف</option>
                                    {{-- سيتم ملء هذا الحقل بواسطة JavaScript بناءً على اختيار الطابق --}}
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="status_filter" class="form-label">الحالة</label>
                                <select name="status" id="status_filter" class="form-select">
                                    <option value="">كل الحالات</option>
                                    @foreach ($bedStatuses as $key => $value)
                                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="bed_number_filter" class="form-label">رقم السرير</label>
                                <input type="text" name="bed_number" id="bed_number_filter" class="form-control" value="{{ request('bed_number') }}" placeholder="بحث بالرقم">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">بحث</button>
                                <a href="{{ route('occupancy.beds.index') }}" class="btn btn-light w-100 mt-1">مسح</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12 text-end">
                    <a href="{{ route('occupancy.beds.create') }}" class="btn btn-success">
                        <i class="fa fa-plus"></i> إضافة سرير جديد
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card card-table show-entire">
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif

                            <div class="table-responsive">
                                <table class="table table-hover table-center mb-0 datatable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>الطابق</th>
                                            <th>الغرفة</th>
                                            <th>رقم السرير</th>
                                            <th>الحالة</th>
                                            <th>المريض الشاغل (إذا وجد)</th>
                                            <th class="text-end">إجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($beds as $bed)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $bed->room->floor->name }}</td>
                                                <td>{{ $bed->room->room_number }}</td>
                                                <td>{{ $bed->bed_number }}</td>
                                                <td>
                                                    @if ($bed->status === 'vacant') <span class="badge bg-success-light">شاغر</span>
                                                    @elseif ($bed->status === 'occupied') <span class="badge bg-danger-light">مشغول</span>
                                                    @elseif ($bed->status === 'reserved') <span class="badge bg-warning-light">محجوز</span>
                                                    @elseif ($bed->status === 'out_of_service') <span class="badge bg-secondary-light">خارج الخدمة</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($bed->patient)
                                                        <a href="{{ route('patient_management.patients.show', $bed->patient->id) }}">{{ $bed->patient->full_name }} ({{ $bed->patient->file_number }})</a>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <a href="{{ route('occupancy.beds.edit', $bed->id) }}" class="btn btn-sm btn-outline-warning me-1">
                                                        <i class="fa fa-edit"></i> تعديل
                                                    </a>
                                                    <form action="{{ route('occupancy.beds.destroy', $bed->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذا السرير؟')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" {{ $bed->status === 'occupied' ? 'disabled' : '' }}>
                                                            <i class="fa fa-trash"></i> حذف
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">لا توجد أسرة تطابق معايير البحث أو لا توجد أسرة مضافة.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                {{ $beds->links() }}
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
    $(document).ready(function() { // <--- بداية الدالة المجهولة لـ ready
        const floorSelect = $('#floor_id_filter');
        const roomSelect = $('#room_id_filter');

        function populateRooms(floorId, selectedRoomId = null) {
            // ... (محتوى دالة populateRooms كما هو) ...
            // مثال:
            roomSelect.html('<option value="">جار التحميل...</option>');
            roomSelect.prop('disabled', true);

            if (!floorId) {
                roomSelect.html('<option value="">كل الغرف</option>');
                return;
            }

            const dynamicGetRoomsUrl = `{{ url('occupancy/api/floors') }}/${floorId}/rooms`;

            $.ajax({
                url: dynamicGetRoomsUrl,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    roomSelect.html('<option value="">كل الغرف</option>');
                    if (Object.keys(data).length > 0) {
                        $.each(data, function(id, room_number) {
                            roomSelect.append($('<option></option>').attr('value', id).text(room_number));
                        });
                    } else {
                        roomSelect.append($('<option value="" disabled>لا توجد غرف في هذا الطابق</option>'));
                    }
                    if(selectedRoomId) {
                        roomSelect.val(selectedRoomId);
                    }
                    roomSelect.prop('disabled', false);
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching rooms: ", xhr.responseText);
                    roomSelect.html('<option value="">خطأ في تحميل الغرف</option>');
                }
            });
        } // <--- نهاية دالة populateRooms

        const initialFloorId = floorSelect.val();
        const initialRoomId = @json(request('room_id', '')); // السطر 189

        if (initialFloorId) {
            populateRooms(initialFloorId, initialRoomId);
        } else {
            roomSelect.html('<option value="">اختر طابقاً أولاً</option>');
            roomSelect.prop('disabled', true);
        }

        floorSelect.on('change', function() {
            populateRooms($(this).val());
        });

    }); // <--- نهاية الدالة المجهولة لـ ready وإغلاق $(document).ready()
</script>
@endpush