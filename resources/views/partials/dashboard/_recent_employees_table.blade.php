<div class="col-12 col-xl-12">
    <div class="card">
        <div class="card-header pb-0">
            <h4 class="card-title d-inline-block">{{ $title ?? 'أحدث الموظفين' }}</h4>
            {{-- <a href="{{ url('employees') }}" class="float-end patient-views">عرض الكل</a> --}}
        </div>
        <div class="card-block table-dash">
            <div class="table-responsive">
                <table class="table mb-0 border-0 datatable custom-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>اسم الموظف</th>
                            <th>المسمى الوظيفي</th>
                            <th>تاريخ الانضمام</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($employees as $employee_item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="table-image">
                                    <img width="28" height="28" class="rounded-circle"
                                         src="{{ $employee_item->profile_picture_path ? Storage::url($employee_item->profile_picture_path) : URL::asset('/assets/img/profiles/avatar-01.jpg') }}"
                                         alt="">
                                    <h2>{{ $employee_item->full_name }}</h2>
                                </td>
                                <td>{{ $employee_item->job_title }}</td>
                                <td>{{ $employee_item->joining_date ? \Carbon\Carbon::parse($employee_item->joining_date)->format('Y-m-d') : 'غير محدد' }}</td>
                                <td class="text-end">
                                    <div class="dropdown dropdown-action">
                                        <a href="javascript:;" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="#"><i class="fa-solid fa-eye m-r-5"></i> عرض</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">لا يوجد موظفون لعرضهم.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>