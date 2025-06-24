<div class="col-12 col-xl-12">
    <div class="card">
        <div class="card-header pb-0">
            <h4 class="card-title d-inline-block">{{ $title ?? 'قائمة حديثة' }}</h4>
            {{-- <a href="{{ url('patients') }}" class="float-end patient-views">عرض الكل</a> --}}
        </div>
        <div class="card-block table-dash">
            <div class="table-responsive">
                <table class="table mb-0 border-0 datatable custom-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>اسم المريض/البيان</th>
                            @if(!isset($is_assessments) && !isset($is_observations) && !isset($is_dispensed) && !isset($is_lab_requests))
                            <th>العمر</th>
                            <th>تاريخ الوصول/التسجيل</th>
                            <th>التشخيص/الحالة</th>
                            @elseif(isset($is_assessments))
                            <th>الطبيب المُقيِّم</th>
                            <th>تاريخ التقييم</th>
                            <th>نسبة التحسن</th>
                            @elseif(isset($is_observations))
                            <th>الممرض</th>
                            <th>تاريخ الملاحظة</th>
                            <th>محتوى الملاحظة</th>
                            @elseif(isset($is_dispensed))
                            <th>الطبيب</th>
                            <th>تاريخ الصرف</th>
                            <th>الحالة</th>
                             @elseif(isset($is_lab_requests))
                            <th>الطبيب الطالب</th>
                            <th>تاريخ الطلب</th>
                            <th>الحالة</th>
                            @endif
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($patients as $patient_item) {{-- تم تغيير اسم المتغير إلى $patient_item لتجنب التعارض --}}
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="table-image">
                                     @php
                                        $image_path = null;
                                        $display_name = 'غير متوفر';
                                        if (isset($is_assessments) || isset($is_observations) || isset($is_dispensed) || isset($is_lab_requests)) { // For related models
                                            if ($patient_item->patient) {
                                                $image_path = $patient_item->patient->profile_image_path;
                                                $display_name = $patient_item->patient->full_name;
                                            }
                                        } else { // For Patient model directly
                                            $image_path = $patient_item->profile_image_path;
                                            $display_name = $patient_item->full_name;
                                        }
                                    @endphp
                                    <img width="28" height="28" class="rounded-circle"
                                         src="{{ $image_path ? Storage::url($image_path) : URL::asset('/assets/img/profiles/avatar-01.jpg') }}"
                                         alt="">
                                    <h2>{{ $display_name }}</h2>
                                </td>

                                @if(!isset($is_assessments) && !isset($is_observations) && !isset($is_dispensed) && !isset($is_lab_requests)) {{-- Patient List --}}
                                    <td>{{ $patient_item->approximate_age ?? 'غير محدد' }}</td>
                                    <td>{{ $patient_item->arrival_date ? \Carbon\Carbon::parse($patient_item->arrival_date)->format('Y-m-d') : ($patient_item->created_at ? \Carbon\Carbon::parse($patient_item->created_at)->format('Y-m-d') : 'غير محدد') }}</td>
                                    <td>
                                        @if($patient_item->status == 'active') <button class="custom-badge status-green">نشط</button>
                                        @elseif($patient_item->status == 'discharged') <button class="custom-badge status-orange">خروج</button>
                                        @else <button class="custom-badge status-grey">{{ $patient_item->status ?? 'غير معروف' }}</button>
                                        @endif
                                    </td>
                                @elseif(isset($is_assessments)) {{-- Assessment List --}}
                                    <td>{{ $patient_item->assessor->name ?? 'غير محدد' }}</td>
                                    <td>{{ $patient_item->assessment_date_gregorian ? \Carbon\Carbon::parse($patient_item->assessment_date_gregorian)->format('Y-m-d') : 'غير محدد' }}</td>
                                    <td><button class="custom-badge status-blue">{{ $patient_item->overall_improvement_percentage ?? 0 }}%</button></td>
                                @elseif(isset($is_observations)) {{-- Observation List --}}
                                     <td>{{ $patient_item->author->name ?? 'غير محدد' }}</td>
                                    <td>{{ $patient_item->created_at ? \Carbon\Carbon::parse($patient_item->created_at)->format('Y-m-d H:i') : 'غير محدد' }}</td>
                                    <td>{{ Str::limit($patient_item->content, 30) }}</td>
                                @elseif(isset($is_dispensed)) {{-- Dispensed Prescriptions List --}}
                                    <td>{{ $patient_item->doctor->name ?? 'غير محدد' }}</td>
                                    <td>{{ $patient_item->dispensing_date ? \Carbon\Carbon::parse($patient_item->dispensing_date)->format('Y-m-d H:i') : 'غير محدد' }}</td>
                                    <td><button class="custom-badge status-green">تم الصرف</button></td>
                                @elseif(isset($is_lab_requests)) {{-- Lab Requests List --}}
                                    <td>{{ $patient_item->doctor->name ?? 'غير محدد' }}</td>
                                    <td>{{ $patient_item->request_date ? \Carbon\Carbon::parse($patient_item->request_date)->format('Y-m-d H:i') : 'غير محدد' }}</td>
                                    <td><button class="custom-badge status-orange">{{ $patient_item->status }}</button></td>
                                @endif
                                <td class="text-end">
                                    <div class="dropdown dropdown-action">
                                        <a href="javascript:;" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="#"><i class="fa-solid fa-eye m-r-5"></i> عرض</a>
                                            {{-- <a class="dropdown-item" href="#"><i class="fa-solid fa-pen-to-square m-r-5"></i> تعديل</a> --}}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">لا توجد بيانات لعرضها.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>