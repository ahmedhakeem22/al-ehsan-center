<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AttendanceRequest;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AttendanceRequestController extends Controller
{
    // Called by Flutter app to create a request
    public function store(Request $request)
    {
        $employee = $request->user()->employeeRecord;
        $request->validate(['request_type' => 'required|in:check_in,check_out']);
        
        // Prevent duplicate pending requests
        $existingRequest = AttendanceRequest::where('employee_id', $employee->id)
            ->where('status', 'pending')
            ->where('request_type', $request->request_type)
            ->whereDate('created_at', Carbon::today())
            ->first();
            
        if($existingRequest){
            return response()->json(['message' => 'لديك طلب معلق بالفعل.'], 409);
        }
        
        AttendanceRequest::create([
            'employee_id' => $employee->id,
            'request_type' => $request->request_type,
        ]);

        return response()->json(['message' => 'تم إرسال طلب التحضير. يرجى مراجعة مسؤول الموارد البشرية.'], 201);
    }
    
    // Called by Flutter app after scanning QR code
    public function verifyQrCodeAndCheckIn(Request $request, $token)
    {
        $attendanceRequest = AttendanceRequest::where('token', $token)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();

        if (!$attendanceRequest) {
            return response()->json(['message' => 'رمز QR غير صالح أو منتهي الصلاحية.'], 404);
        }

        $employee = $attendanceRequest->employee;
        
        if ($attendanceRequest->request_type === 'check_in') {
             $todayAttendance = $employee->attendance()->whereDate('check_in_time', Carbon::today())->first();
             if ($todayAttendance) {
                $attendanceRequest->update(['status' => 'expired']);
                return response()->json(['message' => 'لقد قمت بتسجيل الحضور لهذا اليوم بالفعل.'], 409);
             }
             Attendance::create([
                'employee_id' => $employee->id,
                'check_in_time' => now(),
                'check_in_ip_address' => $request->ip(),
                'notes' => 'Checked in via QR code request ID: ' . $attendanceRequest->id,
            ]);
            $message = 'تم تسجيل الحضور بنجاح.';

        } else { // check_out
            $attendance = $employee->attendance()->whereDate('check_in_time', Carbon::today())->whereNull('check_out_time')->first();
            if (!$attendance) {
                $attendanceRequest->update(['status' => 'expired']);
                return response()->json(['message' => 'لم يتم العثور على سجل حضور مفتوح لهذا اليوم.'], 404);
            }
            $attendance->update([
                'check_out_time' => now(),
                'check_out_ip_address' => $request->ip(),
                'notes' => 'Checked out via QR code request ID: ' . $attendanceRequest->id,
            ]);
            $message = 'تم تسجيل الانصراف بنجاح.';
        }
        
        $attendanceRequest->update(['status' => 'approved']);
        
        return response()->json(['message' => $message]);
    }
}