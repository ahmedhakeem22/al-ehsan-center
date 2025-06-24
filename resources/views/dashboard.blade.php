@extends('layout.mainlayout')
@section('content')
    <div class="page-wrapper">
        <div class="content">
            @component('components.page-header')
                @slot('title')
                    لوحة التحكم
                @endslot
                @slot('li_1')
                    لوحة تحكم {{ $data['userRole'] ?? 'المستخدم' }}
                @endslot
            @endcomponent

            <div class="good-morning-blk">
                <div class="row">
                    <div class="col-md-6">
                        <div class="morning-user">
                            <h2>صباح الخير, <span>{{ $data['greetingName'] ?? 'المستخدم' }}</span></h2>
                            <p>نتمنى لك يوماً سعيداً في العمل</p>
                        </div>
                    </div>
                    <div class="col-md-6 position-blk">
                        <div class="morning-img">
                            <img src="{{ URL::asset('/assets/img/morning-img-01.png') }}" alt="صورة صباحية">
                        </div>
                    </div>
                </div>
            </div>

            {{-- قسم الإحصائيات الرئيسية --}}
            <div class="row">
                @if($role == 'Admin' || $role == 'Super Admin')
                    @include('dashboard.partials._admin_stats', ['data' => $data])
                @elseif($role == 'Doctor')
                    @include('dashboard.partials._doctor_stats', ['data' => $data])
                @elseif($role == 'Nurse')
                    @include('dashboard.partials._nurse_stats', ['data' => $data])
                @elseif($role == 'Receptionist')
                    @include('dashboard.partials._receptionist_stats', ['data' => $data])
                @elseif($role == 'Pharmacist')
                    @include('dashboard.partials._pharmacist_stats', ['data' => $data])
                @elseif($role == 'Lab Technician')
                    @include('dashboard.partials._lab_technician_stats', ['data' => $data])
                @elseif($role == 'HR Manager')
                    @include('dashboard.partials._hr_manager_stats', ['data' => $data])
                @endif
            </div>

            {{-- قسم المخططات البيانية (يترك كما هو من حيث الهيكل، البيانات تحتاج JavaScript أو تمرير خاص) --}}
            <div class="row">
                <div class="col-12 col-md-12 col-lg-6 col-xl-9">
                    <div class="card">
                        <div class="card-body">
                            <div class="chart-title patient-visit">
                                <h4>زيارات المرضى حسب الجنس</h4>
                                <div>
                                    <ul class="nav chat-user-total">
                                        <li><i class="fa fa-circle current-users" aria-hidden="true"></i> ذكور 75%</li>
                                        <li><i class="fa fa-circle old-users" aria-hidden="true"></i> إناث 25%</li>
                                    </ul>
                                </div>
                                {{-- @livewire('select-dashboard') تم إزالته --}}
                            </div>
                            <div id="patient-chart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-12 col-lg-6 col-xl-3 d-flex">
                    <div class="card">
                        <div class="card-body">
                            <div class="chart-title">
                                <h4>المرضى حسب القسم</h4>
                            </div>
                            <div id="donut-chart-dash" class="chart-user-icon">
                                <img src="{{ URL::asset('/assets/img/icons/user-icon.svg') }}" alt="أيقونة المستخدم">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- قسم الأقسام العليا (مثال للـ Admin) --}}
            @if($role == 'Admin' || $role == 'Super Admin')
            <div class="row">
                <div class="col-12 col-md-12 col-xl-4">
                    <div class="card top-departments">
                        <div class="card-header">
                            <h4 class="card-title mb-0">الأقسام الأعلى أداءً</h4>
                        </div>
                        <div class="card-body">
                            {{-- هذا الجزء كان يعتمد على JSON، يمكنك ملؤه ببيانات ديناميكية إذا توفرت --}}
                            {{-- مثال لبيانات ثابتة مؤقتاً --}}
                            <div class="activity-top">
                                <div class="activity-boxs comman-flex-center">
                                    <img src="{{ URL::asset('/assets/img/icons/dep-icon-01.svg') }}" alt="أيقونة قسم">
                                </div>
                                <div class="departments-list">
                                    <h4>الطب العام</h4>
                                    <p>60%</p>
                                </div>
                            </div>
                            <div class="activity-top mb-0">
                                <div class="activity-boxs comman-flex-center">
                                    <img src="{{ URL::asset('/assets/img/icons/dep-icon-02.svg') }}" alt="أيقونة قسم">
                                </div>
                                <div class="departments-list">
                                    <h4>طب العيون</h4>
                                    <p>25%</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- قسم الجداول (المواعيد، المرضى، الخ) بناءً على الدور --}}
            @if(($role == 'Admin' || $role == 'Super Admin') && isset($data['upcoming_appointments_list']))
                @include('dashboard.partials._upcoming_appointments_table', [
                    'title' => $data['upcoming_appointments_title'],
                    'appointments' => $data['upcoming_appointments_list'],
                    'role' => $role
                ])
            @endif
             {{-- يمكن إضافة حالات أخرى مشابهة لبقية الأدوار التي لديها قوائم لعرضها --}}


            @if(($role == 'Admin' || $role == 'Super Admin' || $role == 'Receptionist') && isset($data['recent_patients_list']))
                 @include('dashboard.partials._recent_patients_table', [
                    'title' => $data['recent_patients_list_title'],
                    'patients' => $data['recent_patients_list'],
                    'role' => $role
                ])
            @elseif($role == 'Doctor' && isset($data['recentAssessments']))
                 @include('dashboard.partials._recent_patients_table', [ /* استخدام نفس القالب مع بيانات التقييمات */
                    'title' => $data['recentAssessmentsTitle'],
                    'patients' => $data['recentAssessments'], // لاحظ أن هذا سيكون قائمة تقييمات وليس مرضى مباشرة
                    'role' => $role,
                    'is_assessments' => true // متغير إضافي للتمييز
                ])
            @elseif($role == 'Nurse' && isset($data['recentObservations']))
                 @include('dashboard.partials._recent_patients_table', [
                    'title' => $data['recentObservationsTitle'],
                    'patients' => $data['recentObservations'], // قائمة ملاحظات
                    'role' => $role,
                    'is_observations' => true
                ])
            @elseif($role == 'Pharmacist' && isset($data['recentlyDispensed']))
                 @include('dashboard.partials._recent_patients_table', [
                    'title' => $data['recentlyDispensedTitle'],
                    'patients' => $data['recentlyDispensed'], // قائمة وصفات
                    'role' => $role,
                    'is_dispensed' => true
                ])
            @elseif($role == 'Lab Technician' && isset($data['recentRequestsForResults']))
                 @include('dashboard.partials._recent_patients_table', [
                    'title' => $data['recentRequestsForResultsTitle'],
                    'patients' => $data['recentRequestsForResults'], // قائمة طلبات فحوصات
                    'role' => $role,
                    'is_lab_requests' => true
                ])
             @elseif($role == 'HR Manager' && isset($data['recentHires']))
                 @include('dashboard.partials._recent_employees_table', [
                    'title' => $data['recentHiresTitle'],
                    'employees' => $data['recentHires']
                ])
            @endif
            </div>

        </div>
        @component('components.notification-box', ['notifications_list' => $notifications_for_box])
        @endcomponent
    </div>
@endsection