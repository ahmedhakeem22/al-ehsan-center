<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Services\AttendanceService;
use App\Services\BiometricService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laragear\WebAuthn\Exceptions\AssertionException;

class AttendanceController extends Controller
{
    protected $attendanceService;
    protected $biometricService;

    public function __construct(AttendanceService $attendanceService, BiometricService $biometricService)
    {
        $this->attendanceService = $attendanceService;
        $this->biometricService = $biometricService;
    }

    /**
     * عرض صفحة تسجيل الحضور/الانصراف الرئيسية للموظف.
     */
    public function index()
    {
        $employee = Auth::user()->employeeRecord;
        if (!$employee) {
            return redirect()->route('employee.dashboard')->with('error', 'السجل الوظيفي غير موجود.');
        }

        // تحقق أولاً إذا كان الموظف قد سجل بصمته
        if (!$employee->fingerprints()->where('is_active', true)->exists()) {
            return redirect()->route('employee.fingerprint.register.form')
                             ->with('info', 'يجب عليك تسجيل بصمتك أولاً قبل استخدام نظام الحضور.');
        }

        $statusData = $this->attendanceService->getTodayAttendanceStatus($employee);

        return view('employee.attendance.index', [
            'status' => $statusData['status'],
            'record' => $statusData['record']
        ]);
    }
    
    /**
     * التعامل مع عملية تسجيل الحضور (Check-in)
     * سيتم استدعاء هذا عبر AJAX بعد التحقق من البصمة.
     */
    public function checkIn(Request $request)
    {
        $employee = Auth::user()->employeeRecord;
        
        try {
            // الخطوة 1: التحقق من صحة البصمة
            $this->biometricService->verifyAuthentication($request, $employee);

            // الخطوة 2: تسجيل الحضور
            $record = $this->attendanceService->checkIn($employee, $request->ip(), $request->userAgent());

            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل حضورك بنجاح في الساعة ' . $record->check_in_time->format('H:i:s'),
            ]);

        } catch (AssertionException $e) {
            return response()->json(['success' => false, 'message' => 'فشل التحقق من البصمة: ' . $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'خطأ: ' . $e->getMessage()], 400);
        }
    }
    
    /**
     * التعامل مع عملية تسجيل الانصراف (Check-out)
     * سيتم استدعاء هذا عبر AJAX بعد التحقق من البصمة.
     */
    public function checkOut(Request $request)
    {
        $employee = Auth::user()->employeeRecord;
        
        try {
            // الخطوة 1: التحقق من صحة البصمة
            $this->biometricService->verifyAuthentication($request, $employee);

            // الخطوة 2: تسجيل الانصراف
            $record = $this->attendanceService->checkOut($employee);

            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل انصرافك بنجاح في الساعة ' . $record->check_out_time->format('H:i:s'),
            ]);
        } catch (AssertionException $e) {
            return response()->json(['success' => false, 'message' => 'فشل التحقق من البصمة: ' . $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'خطأ: ' . $e->getMessage()], 400);
        }
    }
    
    /**
     * عرض سجل الحضور الشخصي للموظف
     */
    public function history()
    {
        $employee = Auth::user()->employeeRecord;
        $records = $employee->attendanceRecords()->latest('attendance_date')->paginate(15);
        
        return view('employee.attendance.history', compact('records'));
    }
}