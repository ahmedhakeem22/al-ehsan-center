<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\EmployeeShift;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class AttendanceReportController extends Controller
{
  /**
   * يعرض لوحة معلومات تحتوي على مؤشرات الأداء الرئيسية للحضور في يوم محدد.
   */
  public function dashboard(Request $request)
  {
    $selectedDate = $request->input('date', today()->toDateString());
    $date = Carbon::parse($selectedDate);
    $gracePeriod = 15;

    // 1. البيانات القائمة على المناوبات (للممرضين، الحراسة..)
    $shiftsToday = EmployeeShift::with(['employee', 'definition'])
      ->whereDate('shift_date', $date)
      ->get();
    $attendanceToday = Attendance::with('employee')
      ->whereDate('check_in_time', $date)
      ->get()->keyBy('employee_id');

    $presentScheduledIds = $attendanceToday->keys();
    $absentEmployees = $shiftsToday->filter(fn($shift) => !$date->isFuture() && !$presentScheduledIds->contains($shift->employee_id));
    $lateArrivals = $attendanceToday->filter(fn($att) => $att->isLate($gracePeriod));

    // 2. الحضور غير المجدول (للإداريين وغيرهم)
    // نحضر سجلات الحضور التي ليس لها مناوبة مرتبطة في نفس اليوم
    $unscheduledAttendance = Attendance::with('employee')
      ->whereDate('check_in_time', $date)
      ->where(function ($query) {
        // تحقق من عدم وجود مناوبة مطابقة باستخدام subquery صريح
        $query->whereNotExists(function ($subQuery) {
          $subQuery->selectRaw(1)
            ->from('employee_shifts')
            // الشرط الأول: تطابق معرف الموظف
            ->whereColumn('employee_shifts.employee_id', 'attendances.employee_id')
            // الشرط الثاني: تطابق تاريخ المناوبة مع تاريخ الحضور
            ->whereRaw('employee_shifts.shift_date = DATE(attendances.check_in_time)');
        });
      })
      ->get();

    // 3. تجميع الإحصائيات
    $stats = [
      'totalScheduled' => $shiftsToday->count(),
      'presentScheduled' => $shiftsToday->filter(fn($s) => $presentScheduledIds->contains($s->employee_id))->count(),
      'presentUnscheduled' => $unscheduledAttendance->count(),
      'absentCount' => $absentEmployees->count(),
      'lateCount' => $lateArrivals->count(),
    ];
    $stats['totalPresent'] = $stats['presentScheduled'] + $stats['presentUnscheduled'];


    return view('hr.attendance_reports.dashboard', compact(
      'date',
      'stats',
      'absentEmployees',
      'lateArrivals',
      'unscheduledAttendance'
    ));
  }

  /**
   * عرض قائمة شاملة بسجلات الحضور والغياب مع فلترة متقدمة.
   */
  public function index(Request $request)
  {
    $employees = Employee::whereHas('user', fn($q) => $q->where('is_active', true))
      ->orderBy('full_name')->pluck('full_name', 'id');

    $dateFrom = $request->filled('date_from') ? Carbon::parse($request->date_from) : today()->subMonth();
    $dateTo = $request->filled('date_to') ? Carbon::parse($request->date_to) : today();
    $gracePeriod = 15;

    // ---- بناء مجموعة البيانات المدمجة ----

    // 1. جلب البيانات من المناوبات (للحاضرين، المتأخرين، الغائبين المجدولين)
    $shiftsQuery = EmployeeShift::with(['employee', 'definition', 'attendanceOnDate'])
      ->whereBetween('shift_date', [$dateFrom, $dateTo]);
    if ($request->filled('employee_id')) {
      $shiftsQuery->where('employee_id', $request->employee_id);
    }
    $shiftBasedData = $shiftsQuery->get()->map(function ($shift) use ($gracePeriod) {
      $attendance = $shift->attendanceOnDate;
      $status = 'absent';
      if ($attendance) {
        $status = $attendance->isLate($gracePeriod) ? 'late' : 'present';
      }
      if ($shift->shift_date->isAfter(today())) {
        $status = 'scheduled';
      }
      return [
        'date' => $shift->shift_date,
        'employee' => $shift->employee,
        'shift_definition' => $shift->definition,
        'attendance' => $attendance,
        'status' => $status,
      ];
    });

    // 2. جلب البيانات من الحضور غير المجدول (للإداريين)
    $attendanceQuery = Attendance::with('employee')
      ->whereBetween('check_in_time', [$dateFrom->startOfDay(), $dateTo->endOfDay()])
      ->where(function ($query) {
        $query->whereNotExists(function ($subQuery) {
          $subQuery->selectRaw(1)
            ->from('employee_shifts')
            ->whereColumn('employee_shifts.employee_id', 'attendances.employee_id')
            ->whereRaw('employee_shifts.shift_date = DATE(attendances.check_in_time)');
        });
      });

    if ($request->filled('employee_id')) {
      $attendanceQuery->where('employee_id', $request->employee_id);
    }
    $attendanceBasedData = $attendanceQuery->get()->map(function ($attendance) {
      return [
        'date' => $attendance->check_in_time->toDate(),
        'employee' => $attendance->employee,
        'shift_definition' => null, // لا توجد مناوبة
        'attendance' => $attendance,
        'status' => 'unscheduled', // حالة جديدة
      ];
    });

    // 3. دمج المجموعتين وفرزهم حسب التاريخ
    $reportData = $shiftBasedData->concat($attendanceBasedData)->sortByDesc('date');

    // 4. تطبيق فلتر الحالة بعد الدمج
    $statusFilter = $request->input('status');
    if ($statusFilter) {
      $reportData = $reportData->filter(function ($item) use ($statusFilter) {
        if ($statusFilter === 'present_on_time') {
          return $item->status === 'present';
        }
        return $item->status === $statusFilter;
      });
    }

    $paginatedData = $this->manualPaginate($reportData, 20, $request);

    return view('hr.attendance_reports.index', [
      'reportData' => $paginatedData,
      'employees' => $employees,
    ]);
  }

  /**
   * عرض صفحة مفصلة لسجلات موظف معين.
   */
  public function show(Request $request, Employee $employee)
  {
    $dateFrom = $request->filled('date_from') ? Carbon::parse($request->date_from) : today()->subMonth();
    $dateTo = $request->filled('date_to') ? Carbon::parse($request->date_to) : today();
    $gracePeriod = 15;

    // 1. جلب المناوبات المجدولة
    $shifts = EmployeeShift::with(['definition', 'attendanceOnDate'])
      ->where('employee_id', $employee->id)
      ->whereBetween('shift_date', [$dateFrom, $dateTo])
      ->get();

    // 2. جلب الحضور غير المجدول
    $unscheduledAttendanceQuery = Attendance::where('employee_id', $employee->id)
      ->whereBetween('check_in_time', [$dateFrom->startOfDay(), $dateTo->endOfDay()])
      ->where(function ($query) {
        $query->whereNotExists(function ($subQuery) {
          $subQuery->selectRaw(1)
            ->from('employee_shifts')
            ->whereColumn('employee_shifts.employee_id', 'attendances.employee_id')
            ->whereRaw('employee_shifts.shift_date = DATE(attendances.check_in_time)');
        });
      });
    $unscheduledAttendance = $unscheduledAttendanceQuery->get();

    // 3. بناء التقرير المدمج

    // معالجة بيانات المناوبات
    $shiftData = $shifts->map(function ($shift) use ($gracePeriod) {
      $attendance = $shift->attendanceOnDate;
      $status = 'absent';
      if ($attendance) {
        $status = $attendance->isLate($gracePeriod) ? 'late' : 'present';
      }
      if ($shift->shift_date->isAfter(today())) {
        $status = 'scheduled';
      }
      return [
        'date' => $shift->shift_date,
        'employee' => $shift->employee,
        'shift_definition' => $shift->definition,
        'attendance' => $attendance,
        'status' => $status,
      ];
    });

    // معالجة بيانات الحضور غير المجدول
    $attendanceData = $unscheduledAttendance->map(function ($attendance) {
      return [
        'date' => $attendance->check_in_time->toDate(),
        'employee' => $attendance->employee,
        'shift_definition' => null,
        'attendance' => $attendance,
        'status' => 'unscheduled',
      ];
    });

    $reportData = $shiftData->concat($attendanceData)->sortByDesc('date');

    // 4. إعادة حساب الإحصائيات لتكون أكثر دقة
    $stats = [
      'scheduled_days' => $shifts->count(),
      'present_days' => 0,
      'absent_days' => 0,
      'late_arrivals' => 0,
      'unscheduled_presence' => $unscheduledAttendance->count(),
      'total_worked_hours' => 0,
    ];

    // تصحيح حلقة foreach لتستخدم صيغة المصفوفة
    foreach ($reportData as $record) {
      if ($record['attendance']) {
        $stats['present_days']++;
        $stats['total_worked_hours'] += $record['attendance']->worked_hours ?? 0;
      }
      if ($record['status'] === 'late')
        $stats['late_arrivals']++;
      if ($record['status'] === 'absent')
        $stats['absent_days']++;
    }

    $totalPresentAndScheduled = $stats['scheduled_days'] - $stats['absent_days'];
    $stats['attendance_percentage'] = $stats['scheduled_days'] > 0 ? round(($totalPresentAndScheduled / $stats['scheduled_days']) * 100) : 0;
    $stats['average_worked_hours'] = $stats['present_days'] > 0 ? round($stats['total_worked_hours'] / $stats['present_days'], 2) : 0;

    $paginatedData = $this->manualPaginate($reportData, 20, $request);

    return view('hr.attendance_reports.show', compact('employee', 'paginatedData', 'stats', 'dateFrom', 'dateTo'));
  }

  private function manualPaginate($items, $perPage, Request $request)
  {
    $currentPage = Paginator::resolveCurrentPage() ?: 1;
    $currentItems = $items->slice(($currentPage - 1) * $perPage, $perPage)->values();
    return new LengthAwarePaginator($currentItems, $items->count(), $perPage, $currentPage, [
      'path' => Paginator::resolveCurrentPath(),
      'query' => $request->query(),
    ]);
  }
}