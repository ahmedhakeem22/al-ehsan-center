<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\AttendanceRecord;
use Carbon\Carbon;

class AttendanceService
{
    /**
     * Get the current attendance status for an employee for today.
     *
     * @param Employee $employee
     * @return array ['status' => string, 'record' => ?AttendanceRecord]
     */
    public function getTodayAttendanceStatus(Employee $employee): array
    {
        $todayRecord = AttendanceRecord::where('employee_id', $employee->id)
            ->whereDate('attendance_date', Carbon::today())
            ->first();

        if (!$todayRecord) {
            return ['status' => 'not_checked_in', 'record' => null]; // لم يسجل حضور بعد
        }

        if ($todayRecord->check_in_time && !$todayRecord->check_out_time) {
            return ['status' => 'checked_in', 'record' => $todayRecord]; // سجل حضور ولم يسجل انصراف
        }

        if ($todayRecord->check_in_time && $todayRecord->check_out_time) {
            return ['status' => 'checked_out', 'record' => $todayRecord]; // أكمل الدوام
        }
        
        // حالة غير متوقعة
        return ['status' => 'error', 'record' => $todayRecord];
    }

    /**
     * Record employee check-in.
     *
     * @param Employee $employee
     * @param string $ipAddress
     * @param string $userAgent
     * @return AttendanceRecord
     */
    public function checkIn(Employee $employee, string $ipAddress, string $userAgent): AttendanceRecord
    {
        $status = $this->getTodayAttendanceStatus($employee);

        // لا يمكن تسجيل الحضور إذا كان قد سجل بالفعل
        if ($status['status'] !== 'not_checked_in') {
            throw new \Exception('لا يمكن تسجيل الحضور مرة أخرى لهذا اليوم.');
        }

        return AttendanceRecord::create([
            'employee_id' => $employee->id,
            'attendance_date' => Carbon::today(),
            'check_in_time' => now(),
            'check_in_method' => 'fingerprint',
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);
    }

    /**
     * Record employee check-out.
     *
     * @param Employee $employee
     * @return AttendanceRecord
     */
    public function checkOut(Employee $employee): AttendanceRecord
    {
        $status = $this->getTodayAttendanceStatus($employee);

        // لا يمكن تسجيل الانصراف إلا إذا كان قد سجل حضورًا فقط
        if ($status['status'] !== 'checked_in') {
            throw new \Exception('لا يمكن تسجيل الإنصراف. يجب تسجيل الحضور أولاً أو قد تم تسجيل الإنصراف بالفعل.');
        }

        $record = $status['record'];
        $record->update([
            'check_out_time' => now(),
            'check_out_method' => 'fingerprint',
        ]);
        
        return $record;
    }
}