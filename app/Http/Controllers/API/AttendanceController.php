<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function checkIn(Request $request)
    {
        $employee = $request->user()->employeeRecord;

        $todayAttendance = $employee->attendance()
            ->whereDate('check_in_time', Carbon::today())
            ->first();

        if ($todayAttendance) {
            return response()->json(['message' => 'لقد قمت بتسجيل الحضور لهذا اليوم بالفعل.'], 409);
        }

        $attendance = Attendance::create([
            'employee_id' => $employee->id,
            'check_in_time' => now(),
            'check_in_ip_address' => $request->ip(),
        ]);

        return response()->json([
            'message' => 'تم تسجيل الحضور بنجاح.',
            'attendance' => $attendance
        ], 201);
    }

    public function checkOut(Request $request)
    {
        $employee = $request->user()->employeeRecord;

        $attendance = $employee->attendance()
            ->whereDate('check_in_time', Carbon::today())
            ->whereNull('check_out_time')
            ->first();

        if (!$attendance) {
            return response()->json(['message' => 'لم يتم العثور على سجل حضور مفتوح لهذا اليوم.'], 404);
        }

        $attendance->update([
            'check_out_time' => now(),
            'check_out_ip_address' => $request->ip(),
        ]);

        return response()->json([
            'message' => 'تم تسجيل الانصراف بنجاح.',
            'attendance' => $attendance
        ]);
    }
    
    public function getAttendanceStatus(Request $request)
    {
        $employee = $request->user()->employeeRecord;
        
        $lastAttendance = $employee->attendance()
            ->whereDate('check_in_time', Carbon::today())
            ->latest('check_in_time')
            ->first();

        if (!$lastAttendance) {
            return response()->json(['status' => 'not_checked_in']); // لم يسجل حضور اليوم
        }

        if ($lastAttendance && is_null($lastAttendance->check_out_time)) {
            return response()->json(['status' => 'checked_in']); // سجل حضور ولم يسجل انصراف
        }
        
        return response()->json(['status' => 'checked_out']); // سجل حضور وانصراف
    }
}