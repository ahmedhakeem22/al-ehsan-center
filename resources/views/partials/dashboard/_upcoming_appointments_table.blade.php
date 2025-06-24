<div class="col-12 col-md-12 col-xl-8"> {{-- أو col-xl-12 إذا كان هذا هو الجزء الوحيد في الصف --}}
    <div class="card">
        <div class="card-header">
            <h4 class="card-title d-inline-block">{{ $title ?? 'قائمة حديثة' }}</h4>
            {{-- <a href="{{ url('appointments') }}" class="patient-views float-end">عرض الكل</a> --}}
        </div>
        <div class="card-body p-0 table-dash">
            <div class="table-responsive">
                <table class="table mb-0 border-0 datatable custom-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>اسم المريض</th>
                            <th>الطبيب/المُقيِّم</th>
                            <th>التاريخ</th>
                            <th>الوصف/الحالة</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($appointments as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @if($item->patient)
                                    <img width="28" height="28" class="rounded-circle me-2"
                                         src="{{ $item->patient->profile_image_path ? Storage::url($item->patient->profile_image_path) : URL::asset('/assets/img/profiles/avatar-01.jpg') }}"
                                         alt="{{ $item->patient->full_name ?? '' }}">
                                    {{ $item->patient->full_name ?? 'غير محدد' }}
                                    @else
                                    مريض غير محدد
                                    @endif
                                </td>
                                <td class="table-image appoint-doctor">
                                    @if(isset($item->assessor)) {{-- حالة التقييمات --}}
                                        {{-- صورة المقيم إذا متوفرة --}}
                                        <h2>{{ $item->assessor->name ?? 'غير محدد' }}</h2>
                                    @elseif(isset($item->doctor)) {{-- حالة المواعيد الفعلية --}}
                                         {{-- صورة الطبيب إذا متوفرة --}}
                                        <h2>{{ $item->doctor->name ?? 'غير محدد' }}</h2>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="appoint-time">
                                    @if(isset($item->assessment_date_gregorian))
                                        <span>{{ \Carbon\Carbon::parse($item->assessment_date_gregorian)->format('Y-m-d') }}</span>
                                    @elseif(isset($item->appointment_date))
                                        <span>{{ \Carbon\Carbon::parse($item->appointment_date)->format('Y-m-d') }}</span>
                                        @if(isset($item->appointment_time))
                                            الساعة {{ \Carbon\Carbon::parse($item->appointment_time)->format('H:i A') }}
                                        @endif
                                    @else
                                        تاريخ غير محدد
                                    @endif
                                </td>
                                <td>
                                    @if(isset($item->overall_improvement_percentage))
                                        <button class="custom-badge status-blue">تحسن {{ $item->overall_improvement_percentage }}%</button>
                                    @elseif(isset($item->disease))
                                        <button class="custom-badge status-green">{{ $item->disease }}</button>
                                    @else
                                        <button class="custom-badge status-grey">لا يوجد وصف</button>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="dropdown dropdown-action">
                                        <a href="javascript:;" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="#"><i class="fa-solid fa-pen-to-square m-r-5"></i> تعديل</a>
                                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_patient"><i class="fa fa-trash-alt m-r-5"></i> حذف</a>
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