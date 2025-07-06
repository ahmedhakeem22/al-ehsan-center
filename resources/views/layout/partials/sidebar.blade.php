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

                <li class="submenu">
                    <a href="javascript:;" class="{{ Request::is('occupancy*') ? 'active subdrop' : '' }}">
                        <span class="menu-side">
                            <i class="fas fa-hospital-user"
                                style="color: #6c757d; font-size: 1.2rem; margin-right: 5px;"></i>
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

                {{-- ============================================= --}}
                {{--        قسم الموارد البشرية (HR Section)        --}}
                {{-- ============================================= --}}
                <li class="menu-title">الموارد البشرية</li>

                {{-- قائمة الموظفين --}}
                <li class="submenu">
                    <a href="javascript:;"
                        class="{{ Request::is('hr/employees*') || Request::is('hr/attendance-requests*') ? 'active subdrop' : '' }}">
                        <span class="menu-side">
                            <i class="fas fa-users" style="color: #6c757d; font-size: 1.2rem; margin-right: 5px;"></i>
                        </span>
                        <span>الموظفون</span> <span class="menu-arrow"></span>
                    </a>
                    <ul
                        style="{{ Request::is('hr/employees*') || Request::is('hr/attendance-requests*') ? 'display: block;' : 'display: none;' }}">
                        <li>
                            <a class="{{ Request::routeIs('hr.employees.index') || Request::routeIs('hr.employees.show') || Request::routeIs('hr.employees.edit') ? 'active' : '' }}"
                                href="{{ route('hr.employees.index') }}">قائمة الموظفين</a>
                        </li>
                        <li>
                            <a class="{{ Request::routeIs('hr.employees.create') ? 'active' : '' }}"
                                href="{{ route('hr.employees.create') }}">إضافة موظف جديد</a>
                        </li>

                    </ul>
                </li>

                {{-- قائمة المناوبات --}}
                <li class="submenu">
                    <a href="javascript:;"
                        class="{{ Request::is('hr/employee-shifts*') || Request::is('hr/shift-definitions*') ? 'active subdrop' : '' }}">
                        <span class="menu-side">
                            <i class="fas fa-calendar-alt"
                                style="color: #6c757d; font-size: 1.2rem; margin-right: 5px;"></i>
                        </span>
                        <span>المناوبات</span> <span class="menu-arrow"></span>
                    </a>
                    <ul
                        style="{{ Request::is('hr/employee-shifts*') || Request::is('hr/shift-definitions*') ? 'display: block;' : 'display: none;' }}">
                        <li>
                            <a class="{{ Request::is('hr/employee-shifts/calendar') ? 'active' : '' }}"
                                href="{{ route('hr.employee_shifts.calendar') }}">تقويم المناوبات</a>
                        </li>
                        <li>
                            <a class="{{ Request::is('hr/employee-shifts') && !Request::is('hr/employee-shifts/calendar') ? 'active' : '' }}"
                                href="{{ route('hr.employee_shifts.index') }}">جدول المناوبات</a>
                        </li>
                        <li>
                            <a class="{{ Request::is('hr/shift-definitions*') ? 'active' : '' }}"
                                href="{{ route('hr.shift_definitions.index') }}">إعدادات المناوبات</a>
                        </li>
                        <li>
                            <a class="{{ Request::routeIs('hr.shift_definitions.index') || Request::routeIs('hr.shift_definitions.edit') ? 'active' : '' }}"
                                href="{{ route('hr.shift_definitions.index') }}">تعريفات المناوبات</a>
                        </li>
                    </ul>
                </li>

                {{-- الحضور والانصراف --}}
                <li class="submenu">
                    <a href="javascript:;" class="{{ Request::is('hr/attendance-reports*') ? 'active subdrop' : '' }}">
                        <i class="fas fa-clipboard-list"></i> <span> الحضور والانصراف</span> <span
                            class="menu-arrow"></span></a>
                    <ul style="{{ Request::is('hr/attendance-reports*') ? 'display: block;' : 'display: none;' }}">
                        <li>
                            <a class="{{ Request::is('hr/attendance-requests*') ? 'active' : '' }}"
                                href="{{ route('hr.attendance-requests.index') }}"> طلبات الحضور</a>
                        </li>

                        <li>
                            <a href="{{ route('hr.attendance-reports.dashboard') }}"
                                class="{{ Request::routeIs('hr.attendance-reports.dashboard') ? 'active' : '' }}">لوحة
                                المعلومات</a>
                        </li>
                        <li>
                            <a href="{{ route('hr.attendance-reports.index') }}"
                                class="{{ Request::routeIs('hr.attendance-reports.index') ? 'active' : '' }}">التقرير
                                الشامل</a>
                        </li>
                    </ul>
                </li>

                {{-- ============================================= --}}
                {{--        قسم إدارة النظام (Admin Section)      --}}
                {{-- ============================================= --}}
                <li class="menu-title">إدارة النظام</li>

                <li class="submenu">
                    <a href="javascript:;" class="{{ Request::is('admin/users*') ? 'active subdrop' : '' }}">
                        <span class="menu-side">
                            <i class="fas fa-user-shield"
                                style="color: #6c757d; font-size: 1.2rem; margin-right: 5px;"></i>
                        </span>
                        <span>إدارة المستخدمين</span> <span class="menu-arrow"></span>
                    </a>
                    <ul style="{{ Request::is('admin/users*') ? 'display: block;' : 'display: none;' }}">
                        <li>
                            <a class="{{ Request::is('admin/users*') && !Request::routeIs('admin.users.create') ? 'active' : '' }}"
                                href="{{ route('admin.users.index') }}">قائمة المستخدمين</a>
                        </li>
                        <li>
                            <a class="{{ Request::routeIs('admin.users.create') ? 'active' : '' }}"
                                href="{{ route('admin.users.create') }}">إضافة مستخدم جديد</a>
                        </li>
                    </ul>
                </li>


                <li class="submenu">
                    <a href="javascript:;" class="{{ Request::is('admin/settings*') ? 'active subdrop' : '' }}">
                        <span class="menu-side">
                            <i class="fas fa-cogs" style="color: #6c757d; font-size: 1.2rem; margin-right: 5px;"></i>
                        </span>
                        <span>الإعدادات</span> <span class="menu-arrow"></span>
                    </a>
                    <ul style="{{ Request::is('admin/settings*') ? 'display: block;' : 'display: none;' }}">
                        <li>
                            <a class="{{ Request::routeIs('admin.settings.index') ? 'active' : '' }}"
                                href="{{ route('admin.settings.index') }}">نظرة عامة على الإعدادات</a>
                        </li>
                        <li>
                            <a class="{{ Request::is('admin/settings/floors*') ? 'active' : '' }}"
                                href="{{ route('admin.settings.floors.index') }}">إدارة الطوابق</a>
                        </li>
                        <li>
                            <a class="{{ Request::is('admin/settings/rooms*') ? 'active' : '' }}"
                                href="{{ route('admin.settings.rooms.index') }}">إدارة الغرف</a>
                        </li>
                        <li>
                            <a class="{{ Request::is('admin/settings/labtests*') ? 'active' : '' }}"
                                href="{{ route('admin.settings.labtests.index') }}">إدارة الفحوصات المخبرية</a>
                        </li>
                    </ul>
                </li>

                <li class="submenu">

                    <a href="javascript:;" class="{{ Request::is('pharmacy*') ? 'active subdrop' : '' }}">
                        <i class="fa fa-pills"></i> <span> الصيدلية</span> <span class="menu-arrow"></span>
                    </a>

                    <ul style="{{ Request::is('pharmacy*') ? 'display: block;' : 'display: none;' }}">
                        <li>

                            <a href="{{ route('pharmacy.medications.index') }}"
                                class="{{ Request::routeIs('pharmacy.medications.*') ? 'active' : '' }}">
                                إدارة الأدوية
                            </a>
                        </li>
                        <li>

                            <a href="{{ route('pharmacy.dispense.index') }}"
                                class="{{ Request::routeIs('pharmacy.dispense.*') ? 'active' : '' }}">
                                صرف الوصفات
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Multi Level Example (يمكنك إزالته إذا لم تكن بحاجة إليه) --}}
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
                <a href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><span
                        class="menu-side"><img src="{{ URL::asset('/assets/img/icons/logout.svg') }}"
                            alt=""></span>
                    <span>تسجيل الخروج</span></a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</div>
