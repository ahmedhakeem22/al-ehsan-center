<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * عرض قائمة شاملة لسجلات الحضور مع فلاتر.
     */
    public function index(Request $request)
    {
        $query = AttendanceRecord::with('employee')->latest('attendance_date');

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('attendance_date', $request->date);
        } else {
            // افتراضياً، عرض سجلات اليوم
            $query->whereDate('attendance_date', Carbon::today());
        }

        $records = $query->paginate(20)->withQueryString();
        $employees = Employee::orderBy('full_name')->pluck('full_name', 'id');

        return view('hr.attendance.index', compact('records', 'employees'));
    }

    /**
     * عرض تفاصيل سجلات موظف معين.
     */
    public function employeeDetails(Request $request, Employee $employee)
    {
        $query = $employee->attendanceRecords()->latest('attendance_date');
        
        // يمكن إضافة فلاتر للتاريخ هنا
        if ($request->filled('month')) {
            $query->whereMonth('attendance_date', $request->month)
                  ->whereYear('attendance_date', $request->year ?? date('Y'));
        }

        $records = $query->paginate(30)->withQueryString();
        
        return view('hr.attendance.employee-details', compact('employee', 'records'));
    }

    // يمكن إضافة دوال للتقارير (reports) هنا لاحقًا
}