<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Services\BiometricService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laragear\WebAuthn\Exceptions\AttestationException;
use Laragear\WebAuthn\Exceptions\AssertionException;

class BiometricController extends Controller
{
    protected $biometricService;

    public function __construct(BiometricService $biometricService)
    {
        $this->biometricService = $biometricService;
    }

    /**
     * عرض صفحة تسجيل بصمة جديدة
     */
    public function showRegistrationForm()
    {
        $employee = Auth::user()->employeeRecord;
        if (!$employee) {
            // يجب أن يكون المستخدم موظفاً
            return redirect()->route('employee.dashboard')->with('error', 'لا يوجد سجل موظف مرتبط بحسابك.');
        }
        
        // التحقق مما إذا كان لديه بصمة مسجلة بالفعل
        if ($employee->fingerprints()->where('is_active', true)->exists()) {
             return redirect()->route('employee.dashboard')->with('info', 'لديك بصمة مسجلة بالفعل.');
        }
        
        return view('employee.attendance.register-fingerprint');
    }

    /**
     * إنشاء تحدي لتسجيل بصمة جديدة وإرساله كـ JSON.
     * سيتم استدعاء هذا عبر AJAX.
     */
    public function generateRegistrationOptions(Request $request)
    {
        $employee = Auth::user()->employeeRecord;
        if (!$employee) {
            return response()->json(['error' => 'Employee record not found.'], 404);
        }

        try {
            $options = $this->biometricService->generateRegistrationOptions($employee);
            // حفظ التحدي في السيشن للتحقق منه لاحقاً
            $request->session()->put('webauthn.attestation.challenge', $options->getChallenge());
            
            return response()->json($options);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to generate options: ' . $e->getMessage()], 500);
        }
    }

    /**
     * التحقق من بيانات البصمة الجديدة وحفظها.
     * سيتم استدعاء هذا عبر AJAX.
     */
    public function verifyAndSaveRegistration(Request $request)
    {
        $employee = Auth::user()->employeeRecord;
        if (!$employee) {
            return response()->json(['error' => 'Employee record not found.'], 404);
        }

        try {
            $this->biometricService->verifyAndSaveRegistration($request, $employee);
            return response()->json(['success' => true, 'message' => 'تم تسجيل بصمتك بنجاح!']);
        } catch (AttestationException $e) {
            return response()->json(['success' => false, 'message' => 'فشل التحقق من البصمة: ' . $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'حدث خطأ غير متوقع: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * إنشاء تحدي للتحقق من بصمة موجودة (للحضور/الانصراف).
     * سيتم استدعاء هذا عبر AJAX.
     */
    public function generateAuthenticationOptions(Request $request)
    {
        $employee = Auth::user()->employeeRecord;
        if (!$employee) {
            return response()->json(['error' => 'Employee record not found.'], 404);
        }

        try {
            $options = $this->biometricService->generateAuthenticationOptions($employee);
            $request->session()->put('webauthn.assertion.challenge', $options->getChallenge());
            
            return response()->json($options);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to generate options: ' . $e->getMessage()], 500);
        }
    }

    /**
     * التحقق من البصمة لعملية الحضور/الانصراف.
     * هذه الدالة لا تسجل الحضور مباشرة، بل تؤكد صحة البصمة فقط.
     * الـ Controller الخاص بالحضور سيستدعيها.
     * هذا غير مستخدم مباشرة الآن، سيتم دمجه في دوال الحضور
     */
}