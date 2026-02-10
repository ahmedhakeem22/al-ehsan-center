@php $page = 'patient-profile'; @endphp
@extends('layout.mainlayout')
@section('title', 'ملف المريض: ' . $patient->full_name)

@section('content')
    <div class="page-wrapper">
        <div class="content">
            @component('components.page-header')
                @slot('title')
                    المرضى
                @endslot
                @slot('li_1')
                    ملف المريض: {{ $patient->full_name }} ({{ $patient->file_number }})
                @endslot
            @endcomponent

            <div class="row">
                <div class="col-md-12">
                    <!-- Patient Head Start -->
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-auto">
                                    <div class="doctor-profile-img">
                                        <img src="{{ $patient->profile_image_path ? Storage::url($patient->profile_image_path) : asset('assets/img/placeholder-user.png') }}" class="img-fluid" alt="صورة المريض" width="150">
                                    </div>
                                </div>
                                <div class="col-md">
                                    <h4 class="mt-2">{{ $patient->full_name }}</h4>
                                    <p class="text-muted">رقم الملف: {{ $patient->file_number }}</p>
  <p>
        العمر التقريبي: {{ $patient->approximate_age ?: 'غير محدد' }} |
        النوع:
        @if($patient->gender == 'male') ذكر
        @elseif($patient->gender == 'female') أنثى
        @elseif($patient->gender == 'other') آخر
        @elseif($patient->gender == 'unknown') غير معروف
        @else غير محدد
        @endif
        | المحافظة: {{ $patient->province ?: 'غير محدد' }}
    </p>                                    <p>تاريخ الوصول: {{ $patient->arrival_date ? \Carbon\Carbon::parse($patient->arrival_date)->format('d/m/Y') : 'غير محدد' }}</p>
                                    <p>الحالة الحالية:
                                        @if($patient->status == 'active') <span class="badge bg-success">نشط</span>
                                        @elseif($patient->status == 'discharged') <span class="badge bg-warning">خروج</span>
                                        @elseif($patient->status == 'deceased') <span class="badge bg-danger">متوفى</span>
                                        @elseif($patient->status == 'transferred') <span class="badge bg-info">محول</span>
                                        @endif
                                    </p>
                                    @if($patient->currentBed)
                                        <p>الموقع الحالي: سرير {{ $patient->currentBed->bed_number }}، غرفة {{ $patient->currentBed->room->room_number }}، طابق {{ $patient->currentBed->room->floor->name }}</p>
                                    @else
                                        <p class="text-danger">غير مسكن حالياً. <a href="{{ route('patient_management.admissions.show_bed_assignment', $patient->id) }}">تسكين المريض</a></p>
                                    @endif
                                     <a href="{{ route('patient_management.patients.edit', $patient->id) }}" class="btn btn-primary btn-sm">تعديل بيانات المريض</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Patient Head End -->

                    <div class="card">
                        <div class="card-body pt-0">
                            <ul class="nav nav-tabs nav-tabs-bottom">
                                <li class="nav-item"><a class="nav-link active" href="#pat_basic_info" data-bs-toggle="tab">معلومات أساسية</a></li>
                                <li class="nav-item"><a class="nav-link" href="#pat_media" data-bs-toggle="tab">الوسائط (صور وفيديو)</a></li>
                                <li class="nav-item"><a class="nav-link" href="#pat_assessments" data-bs-toggle="tab">تقييمات التحسن</a></li>
                                <li class="nav-item"><a class="nav-link" href="#pat_treatment" data-bs-toggle="tab">الخطة العلاجية والوصفات</a></li>
                                <li class="nav-item"><a class="nav-link" href="#pat_lab_tests" data-bs-toggle="tab">الفحوصات المخبرية</a></li>
                                <li class="nav-item"><a class="nav-link" href="#pat_clinical_notes" data-bs-toggle="tab">الملاحظات والتوصيات</a></li>
                            </ul>

                            <div class="tab-content pt-3">
                                <!-- Basic Info Tab -->
                                <div id="pat_basic_info" class="tab-pane fade show active">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h5>الحالة عند الوصول:</h5>
                                            <p>{{ $patient->condition_on_arrival ?: 'لم تسجل.' }}</p>
                                            <hr>
                                            <h5>مسؤول التسجيل:</h5>
                                            <p>{{ $patient->creator->name ?? 'غير معروف' }}</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- /Basic Info Tab -->

                                <!-- Media Tab -->
                                <div id="pat_media" class="tab-pane fade">
                                    <a href="{{ route('patient_management.media.create', $patient->id) }}" class="btn btn-primary btn-sm mb-3">إضافة وسائط جديدة</a>
                                    @include('patient_management.media._media_list_partial', ['mediaItems' => $patient->media()->orderBy('uploaded_at', 'desc')->paginate(10)])
                                </div>
                                <!-- /Media Tab -->

                                <!-- Assessments Tab -->
                                <div id="pat_assessments" class="tab-pane fade">
<a href="{{ route('assessment.functional.create', $patient->id) }}" class="btn btn-info btn-sm mb-3">إضافة تقييم جديد</a>                                    @if($patient->assessments->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>تاريخ التقييم</th>
                                                    <th>المُقيِّم</th>
                                                    <th>نسبة التحسن (%)</th>
                                                    <th>إجراءات</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($patient->assessments()->orderBy('assessment_date_gregorian', 'desc')->get() as $assessment)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($assessment->assessment_date_gregorian)->format('Y-m-d') }} ({{ $assessment->assessment_date_hijri }})</td>
                                                    <td>{{ $assessment->assessor->name ?? 'غير محدد' }}</td>
                                                    <td>{{ $assessment->overall_improvement_percentage ?? 'N/A' }}%</td>
                                                    <td>
<a href="{{ route('assessment.functional.show', [$patient->id, $assessment->id]) }}" class="btn btn-sm btn-outline-primary">عرض</a>                                                        {{-- <a href="{{ route('assessments.edit', [$patient->id, $assessment->id]) }}" class="btn btn-sm btn-outline-secondary">تعديل</a> --}}
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @else
                                        <p>لا توجد تقييمات مسجلة لهذا المريض.</p>
                                    @endif
                                    {{-- TODO: Add chart for improvement trend --}}
                                </div>
                                <!-- /Assessments Tab -->

                                 <!-- Treatment Plan & Prescriptions Tab -->
                                <div id="pat_treatment" class="tab-pane fade">
                                    {{-- Treatment Plans --}}
                                    <h5>الخطط العلاجية</h5>
                                     <a href="{{-- route('clinical.treatment_plans.create', $patient->id) --}}" class="btn btn-info btn-sm mb-3 disabled">إضافة خطة علاجية (قيد التطوير)</a>
                                    @if($patient->treatmentPlans->count() > 0)
                                    <div class="table-responsive mb-4">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>تاريخ البدء</th>
                                                    <th>الطبيب</th>
                                                    <th>التشخيص</th>
                                                    <th>الحالة</th>
                                                    <th>إجراءات</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($patient->treatmentPlans as $plan)
                                                <tr>
                                                    <td>{{ $plan->start_date ? \Carbon\Carbon::parse($plan->start_date)->format('Y-m-d') : ''}}</td>
                                                    <td>{{ $plan->doctor->name ?? 'غير محدد' }}</td>
                                                    <td>{{ Str::limit($plan->diagnosis, 50) }}</td>
                                                    <td>{{ $plan->status }}</td>
                                                    <td>
                                                         <a href="{{-- route('clinical.treatment_plans.show', [$patient->id, $plan->id]) --}}" class="btn btn-sm btn-outline-primary disabled">عرض</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @else
                                        <p>لا توجد خطط علاجية مسجلة.</p>
                                    @endif

                                    {{-- Prescriptions --}}
                                    <h5>الوصفات الطبية</h5>
                                    <a href="{{-- route('clinical.prescriptions.create', $patient->id) --}}" class="btn btn-warning btn-sm mb-3 disabled">إضافة وصفة طبية (قيد التطوير)</a>
                                     @if($patient->prescriptions->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>تاريخ الوصفة</th>
                                                    <th>الطبيب</th>
                                                    <th>الحالة</th>
                                                    <th>إجراءات</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($patient->prescriptions as $prescription)
                                                <tr>
                                                    <td>{{ $prescription->prescription_date ? \Carbon\Carbon::parse($prescription->prescription_date)->format('Y-m-d H:i') : '' }}</td>
                                                    <td>{{ $prescription->doctor->name ?? 'غير محدد' }}</td>
                                                    <td>{{ $prescription->status }}</td>
                                                    <td>
                                                        <a href="{{-- route('clinical.prescriptions.show', [$patient->id, $prescription->id]) --}}" class="btn btn-sm btn-outline-primary disabled">عرض</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @else
                                        <p>لا توجد وصفات طبية مسجلة.</p>
                                    @endif
                                </div>
                                <!-- /Treatment Plan & Prescriptions Tab -->

                                <!-- Lab Tests Tab -->
                                <div id="pat_lab_tests" class="tab-pane fade">
                                     <a href="{{-- route('clinical.lab_tests.request', $patient->id) --}}" class="btn btn-secondary btn-sm mb-3 disabled">طلب فحص مخبري (قيد التطوير)</a>
                                    @if($patient->labTestRequests->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>تاريخ الطلب</th>
                                                    <th>الطبيب الطالب</th>
                                                    <th>الحالة</th>
                                                    <th>تاريخ النتيجة</th>
                                                    <th>إجراءات</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($patient->labTestRequests as $labRequest)
                                                <tr>
                                                    <td>{{ $labRequest->request_date ? \Carbon\Carbon::parse($labRequest->request_date)->format('Y-m-d H:i') : '' }}</td>
                                                    <td>{{ $labRequest->doctor->name ?? 'غير محدد' }}</td>
                                                    <td>{{ $labRequest->status }}</td>
                                                    <td>{{ $labRequest->result_date ? \Carbon\Carbon::parse($labRequest->result_date)->format('Y-m-d H:i') : 'لم تصدر' }}</td>
                                                    <td>
                                                        <a href="{{-- route('clinical.lab_tests.show_request', [$patient->id, $labRequest->id]) --}}" class="btn btn-sm btn-outline-primary disabled">عرض</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @else
                                        <p>لا توجد طلبات فحوصات مخبرية مسجلة.</p>
                                    @endif
                                </div>
                                <!-- /Lab Tests Tab -->

                                <!-- Clinical Notes Tab -->
                                <div id="pat_clinical_notes" class="tab-pane fade">
                                    <a href="{{-- route('clinical.notes.create', $patient->id) --}}" class="btn btn-success btn-sm mb-3 disabled">إضافة ملاحظة جديدة (قيد التطوير)</a>
                                    @if($patient->clinicalNotes->count() > 0)
                                    <div class="list-group">
                                        @foreach($patient->clinicalNotes()->orderBy('created_at', 'desc')->get() as $note)
                                        <div class="list-group-item list-group-item-action flex-column align-items-start mb-2">
                                            <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">
                                                {{ $note->author->name ?? 'غير معروف' }}
                                                <small class="text-muted">({{ $note->author_role }}) - {{ \App\Enums\ClinicalNoteTypeEnum::from($note->note_type)->label() }}</small>
                                            </h6>
                                            <small>{{ $note->created_at->diffForHumans() }}</small>
                                            </div>
                                            <p class="mb-1">{{ $note->content }}</p>
                                            @if($note->is_actioned)
                                                <small class="text-success">تم التنفيذ بواسطة: {{ $note->actionedBy->name ?? '' }} في {{ $note->actioned_at ? \Carbon\Carbon::parse($note->actioned_at)->format('Y-m-d H:i') : '' }}</small><br>
                                                <small class="text-success">ملاحظات التنفيذ: {{ $note->action_notes }}</small>
                                            @endif
                                        </div>
                                        @endforeach
                                    </div>
                                    @else
                                        <p>لا توجد ملاحظات سريرية مسجلة.</p>
                                    @endif
                                </div>
                                <!-- /Clinical Notes Tab -->

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
    // Activate tab if hash is present in URL
    $(document).ready(function(){
        var hash = window.location.hash;
        if(hash){
            $('.nav-tabs a[href="' + hash + '"]').tab('show');
        }

        // Optional: Change hash on tab click
        $('.nav-tabs a').on('shown.bs.tab', function (e) {
            window.location.hash = e.target.hash;
        });
    });
</script>
@endpush