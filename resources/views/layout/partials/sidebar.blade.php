<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="menu-title">Main</li>
                {{-- ... (روابط Dashboard، Doctors، Patients، Assessments، Occupancy كما هي) ... --}}

                <li class="submenu">
                    <a href="javascript:;"
                        class="{{ Request::is('patient-management/patients*') || Request::is('patient-management/admissions*') ? 'active subdrop' : '' }}">
                        <span class="menu-side">
                            <img src="{{ URL::asset('/assets/img/icons/menu-icon-03.svg') }}" alt="">
                        </span>
                        <span>المرضى</span> <span class="menu-arrow"></span>
                    </a>
                    <ul
                        style="{{ Request::is('patient-management/patients*') || Request::is('patient-management/admissions*') ? 'display: block;' : 'display: none;' }}">
                        <li>
                            <a class="{{ Request::routeIs('patient_management.patients.index') ? 'active' : '' }}"
                                href="{{ route('patient_management.patients.index') }}">قائمة المرضى</a>
                        </li>
                        <li>
                            <a class="{{ Request::routeIs('patient_management.admissions.show_registration') ? 'active' : '' }}"
                                href="{{ route('patient_management.admissions.show_registration') }}">تسجيل مريض
                                جديد</a>
                        </li>

                    </ul>
                </li>

                {{-- <li class="submenu">
                    <a href="javascript:;"
                        class="{{ Request::is('patient-management/patients/*/assessments*') ? 'active subdrop' : '' }}">
                        <span class="menu-side">
                            <i class="fas fa-clipboard-check"
                                style="color: #6c757d; font-size: 1.2rem; margin-right: 5px;"></i>
                        </span>
                        <span>تقييمات التحسن</span> <span class="menu-arrow"></span>
                    </a>
                    <ul
                        style="{{ Request::is('patient-management/patients/*/assessments*') ? 'display: block;' : 'display: none;' }}">
                        <li>
                            <a class="{{ Request::routeIs('assessment.functional.create', ['patient' => '*']) ? 'active' : '' }}"
                                href="{{ route('patient_management.patients.index') }}">إضافة تقييم (اختر مريضاً)</a>
                        </li>
                        <li>
                            <a class="{{ Request::routeIs('assessment.functional.index', ['patient' => '*']) ? 'active' : '' }}"
                                href="{{ route('patient_management.patients.index') }}">عرض التقييمات (اختر مريضاً)</a>
                        </li>
                    </ul>
                </li> --}}

                <li class="submenu">
                    <a href="javascript:;" class="{{ Request::is('occupancy*') ? 'active subdrop' : '' }}">
                        <span class="menu-side">
                            <i class="fas fa-hospital-user" style="color: #6c757d; font-size: 1.2rem; margin-right: 5px;"></i>
                        </span>
                        <span>إدارة الإشغال</span> <span class="menu-arrow"></span>
                    </a>
                    <ul style="{{ Request::is('occupancy*') ? 'display: block;' : 'display: none;' }}">
                        <li>
                            <a class="{{ Request::routeIs('occupancy.dashboard.index') ? 'active' : '' }}"
                               href="{{ route('occupancy.dashboard.index') }}">لوحة تحكم الإشغال</a>
                        </li>
                        <li>
                            <a class="{{ Request::routeIs('occupancy.beds.index') ? 'active' : '' }}"
                               href="{{ route('occupancy.beds.index') }}">إدارة الأسرة</a>
                        </li>
                        <li>
                            <a class="{{ Request::routeIs('occupancy.beds.create') ? 'active' : '' }}"
                               href="{{ route('occupancy.beds.create') }}">إضافة سرير جديد</a>
                        </li>
                    </ul>
                </li>

                {{-- قسم الإعدادات --}}
                <li class="menu-title">إدارة النظام</li> {{-- عنوان جديد للقسم --}}
                <li class="submenu">
                    <a href="javascript:;" class="{{ Request::is('admin/settings*') ? 'active subdrop' : '' }}">
                        <span class="menu-side">
                            <i class="fas fa-cogs" style="color: #6c757d; font-size: 1.2rem; margin-right: 5px;"></i> {{-- أيقونة للإعدادات --}}
                        </span>
                        <span>الإعدادات</span> <span class="menu-arrow"></span>
                    </a>
                    <ul style="{{ Request::is('admin/settings*') ? 'display: block;' : 'display: none;' }}">
                        <li>
                            <a class="{{ Request::routeIs('admin.settings.index') ? 'active' : '' }}"
                               href="{{ route('admin.settings.index') }}">نظرة عامة على الإعدادات</a>
                        </li>
                        <li>
                            <a class="{{ Request::routeIs('admin.settings.floors.index') || Request::routeIs('admin.settings.floors.create') || Request::routeIs('admin.settings.floors.edit') ? 'active' : '' }}"
                               href="{{ route('admin.settings.floors.index') }}">إدارة الطوابق</a>
                        </li>
                        <li>
                            <a class="{{ Request::routeIs('admin.settings.rooms.index') || Request::routeIs('admin.settings.rooms.create') || Request::routeIs('admin.settings.rooms.edit') ? 'active' : '' }}"
                               href="{{ route('admin.settings.rooms.index') }}">إدارة الغرف</a>
                        </li>
                        <li>
                            <a class="{{ Request::routeIs('admin.settings.labtests.index') || Request::routeIs('admin.settings.labtests.create') || Request::routeIs('admin.settings.labtests.edit') ? 'active' : '' }}"
                               href="{{ route('admin.settings.labtests.index') }}">إدارة الفحوصات المخبرية</a>
                        </li>
                        {{-- يمكنك إضافة روابط لإعدادات أخرى هنا --}}
                    </ul>
                </li>
                {{-- نهاية قسم الإعدادات --}}


                <li class="submenu">
                    <a href="javascript:void(0);"><i class="fa fa-share-alt"></i> <span>Multi Level</span> <span
                            class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        <li class="submenu">
                            <a href="javascript:void(0);"><span>Level 1</span> <span class="menu-arrow"></span></a>
                            <ul style="display: none;">
                                <li><a href="javascript:void(0);"><span>Level 2</span></a></li>
                                <li class="submenu">
                                    <a href="javascript:void(0);"> <span> Level 2</span> <span
                                            class="menu-arrow"></span></a>
                                    <ul style="display: none;">
                                        <li><a href="javascript:void(0);">Level 3</a></li>
                                        <li><a href="javascript:void(0);">Level 3</a></li>
                                    </ul>
                                </li>
                                <li><a href="javascript:void(0);"><span>Level 2</span></a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="javascript:void(0);"><span>Level 1</span></a>
                        </li>
                    </ul>
                </li>
            </ul>
            <div class="logout-btn">
                <a href="{{ url('login') }}"><span class="menu-side"><img
                            src="{{ URL::asset('/assets/img/icons/logout.svg') }}" alt=""></span>
                    <span>Logout</span></a>
            </div>
        </div>
    </div>
</div>