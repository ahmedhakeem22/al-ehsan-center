@php $page = 'occupancy-dashboard'; @endphp
@extends('layout.mainlayout')
@section('title', 'لوحة تحكم الإشغال')

@push('styles')
<style>
    .bed-icon {
        display: inline-block;
        width: 60px; /* أو حسب حجم الأيقونة */
        height: 40px; /* أو حسب حجم الأيقونة */
        line-height: 40px;
        text-align: center;
        border: 1px solid #ccc;
        border-radius: 5px;
        margin: 5px;
        cursor: pointer;
        font-size: 0.8rem;
        position: relative;
    }
    .bed-vacant { background-color: #d4edda; border-color: #c3e6cb; color: #155724; }
    .bed-occupied { background-color: #f8d7da; border-color: #f5c6cb; color: #721c24; }
    .bed-reserved { background-color: #fff3cd; border-color: #ffeeba; color: #856404; }
    .bed-out_of_service { background-color: #e2e3e5; border-color: #d6d8db; color: #383d41; }

    .bed-patient-info {
        position: absolute;
        bottom: -5px; /* يظهر تحت الأيقونة قليلاً */
        left: 50%;
        transform: translateX(-50%);
        font-size: 0.65rem;
        white-space: nowrap;
        background-color: rgba(0,0,0,0.7);
        color: white;
        padding: 2px 4px;
        border-radius: 3px;
        z-index: 10;
        opacity: 0;
        transition: opacity 0.3s;
    }
    .bed-icon:hover .bed-patient-info {
        opacity: 1;
    }
    .room-container {
        border: 1px solid #eee;
        padding: 10px;
        margin-bottom: 15px;
        border-radius: 5px;
    }
    .floor-header {
        background-color: #f8f9fa;
        padding: 10px;
        margin-bottom: 10px;
        border-bottom: 2px solid #dee2e6;
    }
    .room-header{
        font-weight: bold;
        margin-bottom: 5px;
    }
</style>
@endpush

@section('content')
    <div class="page-wrapper">
        <div class="content">
            @component('components.page-header')
                @slot('title')
                    الإشغال
                @endslot
                @slot('li_1')
                    لوحة تحكم الإشغال
                @endslot
            @endcomponent

            <!-- Stats -->
            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <div class="card dash-widget">
                        <div class="card-body">
                            <span class="dash-widget-icon"><i class="fa fa-bed"></i></span>
                            <div class="dash-widget-info">
                                <h3>{{ $stats['totalBeds'] }}</h3>
                                <h6>إجمالي الأسرة</h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card dash-widget">
                        <div class="card-body">
                            <span class="dash-widget-icon text-danger"><i class="fa-solid fa-user-check"></i></span>
                            <div class="dash-widget-info">
                                <h3>{{ $stats['occupiedBeds'] }}</h3>
                                <h6>أسرة مشغولة</h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card dash-widget">
                        <div class="card-body">
                            <span class="dash-widget-icon text-success"><i class="fa-solid fa-user-plus"></i></span>
                            <div class="dash-widget-info">
                                <h3>{{ $stats['vacantBeds'] }}</h3>
                                <h6>أسرة شاغرة</h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card dash-widget">
                        <div class="card-body">
                            <span class="dash-widget-icon text-info"><i class="fas fa-chart-pie"></i></span>
                            <div class="dash-widget-info">
                                <h3>{{ $stats['occupancyPercentage'] }}%</h3>
                                <h6>نسبة الإشغال</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Stats -->

            @foreach ($floors as $floor)
                <div class="card">
                    <div class="card-header floor-header">
                        <h4 class="card-title mb-0">طابق: {{ $floor->name }}</h4>
                        @php
                            $floorBeds = $floor->rooms->reduce(function ($carry, $room) { return $carry + $room->beds->count(); }, 0);
                            $floorOccupiedBeds = $floor->rooms->reduce(function ($carry, $room) { return $carry + $room->beds->where('status', 'occupied')->count(); }, 0);
                            $floorOccupancyPercentage = ($floorBeds > 0) ? round(($floorOccupiedBeds / $floorBeds) * 100, 1) : 0;
                        @endphp
                        <small class="text-muted">إجمالي الأسرة: {{ $floorBeds }} | المشغول: {{ $floorOccupiedBeds }} | نسبة الإشغال: {{ $floorOccupancyPercentage }}%</small>
                    </div>
                    <div class="card-body">
                        @if ($floor->rooms->isEmpty())
                            <p class="text-muted">لا توجد غرف مضافة في هذا الطابق.</p>
                        @else
                            <div class="row">
                                @foreach ($floor->rooms as $room)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="room-container">
                                            <div class="room-header">غرفة رقم: {{ $room->room_number }} (السعة: {{ $room->capacity }})</div>
                                            <div>
                                                @if ($room->beds->isEmpty())
                                                    <p class="text-muted small">لا توجد أسرة في هذه الغرفة.</p>
                                                @else
                                                    @foreach ($room->beds as $bed)
                                                        <span class="bed-icon bed-{{ $bed->status }}"
                                                              title="{{ $bed->bed_number }} - {{ $bedStatuses[$bed->status] ?? $bed->status }}"
                                                              data-bs-toggle="tooltip" data-bs-placement="top"
                                                              onclick="showBedDetails({{ $bed->id }})">
                                                            {{ $bed->bed_number }}
                                                            @if($bed->patient)
                                                            <span class="bed-patient-info">{{ Str::limit($bed->patient->full_name, 10) }}</span>
                                                            @endif
                                                        </span>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach

            @if ($floors->isEmpty())
                <div class="alert alert-info">لا توجد طوابق أو أسرة مضافة في النظام. يرجى <a href="{{-- route('admin.floors.create') --}}">إضافة طوابق</a> ثم <a href="{{ route('occupancy.beds.create') }}">إضافة أسرة</a>.</div>
            @endif

        </div>
        @component('components.notification-box')
        @endcomponent
    </div>

    <!-- Bed Details Modal -->
    <div class="modal fade" id="bedDetailModal" tabindex="-1" aria-labelledby="bedDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bedDetailModalLabel">تفاصيل السرير</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>السرير:</strong> <span id="modalBedNumber"></span></p>
                    <p><strong>الغرفة:</strong> <span id="modalRoomNumber"></span></p>
                    <p><strong>الطابق:</strong> <span id="modalFloorName"></span></p>
                    <p><strong>الحالة:</strong> <span id="modalBedStatus"></span></p>
                    <div id="modalPatientInfo" style="display:none;">
                        <hr>
                        <h5>معلومات المريض</h5>
                        <div class="text-center mb-2">
                             <img id="modalPatientImage" src="" alt="صورة المريض" class="rounded-circle" width="80" height="80" style="object-fit: cover;">
                        </div>
                        <p><strong>اسم المريض:</strong> <span id="modalPatientName"></span></p>
                        <p><strong>رقم الملف:</strong> <span id="modalPatientFileNumber"></span></p>
                        <a href="#" id="modalPatientProfileLink" class="btn btn-sm btn-primary">عرض ملف المريض</a>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" id="modalEditBedLink" class="btn btn-warning">تعديل السرير</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    function showBedDetails(bedId) {
        $.ajax({
            url: "{{ url('occupancy/dashboard/beds') }}/" + bedId + "/details", // تأكد أن هذا المسار معرف
            type: 'GET',
            dataType: 'json',
            success: function(bed) {
                $('#modalBedNumber').text(bed.bed_number);
                $('#modalRoomNumber').text(bed.room.room_number);
                $('#modalFloorName').text(bed.room.floor.name);

                let statusText = bed.status;
                if(bed.status === 'vacant') statusText = 'شاغر';
                else if(bed.status === 'occupied') statusText = 'مشغول';
                else if(bed.status === 'reserved') statusText = 'محجوز';
                else if(bed.status === 'out_of_service') statusText = 'خارج الخدمة';
                $('#modalBedStatus').html('<span class="badge bg-' + (bed.status === 'vacant' ? 'success' : (bed.status === 'occupied' ? 'danger' : (bed.status === 'reserved' ? 'warning' : 'secondary'))) + '-light">' + statusText + '</span>');


                if (bed.patient) {
                    $('#modalPatientName').text(bed.patient.full_name);
                    $('#modalPatientFileNumber').text(bed.patient.file_number);
                     if(bed.patient.profile_image_url){
                        $('#modalPatientImage').attr('src', bed.patient.profile_image_url).show();
                    } else {
                        $('#modalPatientImage').hide();
                    }
                    $('#modalPatientProfileLink').attr('href', "{{ url('patient-management/patients') }}/" + bed.patient.id);
                    $('#modalPatientInfo').show();
                } else {
                    $('#modalPatientInfo').hide();
                    $('#modalPatientImage').hide();
                }

                $('#modalEditBedLink').attr('href', "{{ url('occupancy/beds') }}/" + bed.id + "/edit");


                var bedDetailModal = new bootstrap.Modal(document.getElementById('bedDetailModal'));
                bedDetailModal.show();
            },
            error: function(xhr) {
                alert('حدث خطأ أثناء جلب تفاصيل السرير.');
                console.error(xhr.responseText);
            }
        });
    }

    // لتفعيل tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@endpush