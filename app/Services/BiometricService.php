<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\EmployeeFingerprint;
use Illuminate\Http\Request;
use Laragear\WebAuthn\Facades\WebAuthn;
use Laragear\WebAuthn\Exceptions\AssertionException;
use Laragear\WebAuthn\Exceptions\AttestationException;

class BiometricService
{
    /**
     * Generate options for fingerprint registration.
     *
     * @param \App\Models\Employee $employee
     * @return \DarkGhostHunter\Larapass\Contracts\WebAuthn\PublicKeyCreationOptions
     */
    public function generateRegistrationOptions(Employee $employee)
    {
        // نطلب من مكتبة WebAuthn إنشاء "تحدي" لتسجيل بصمة جديدة
        // سيتم إرسال هذا التحدي إلى المتصفح
        return WebAuthn::generateAttestation($employee->user);
    }

    /**
     * Verify the registration data and save the new fingerprint credential.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Employee $employee
     * @return \App\Models\EmployeeFingerprint
     * @throws \Laragear\WebAuthn\Exceptions\AttestationException
     */
    public function verifyAndSaveRegistration(Request $request, Employee $employee): EmployeeFingerprint
    {
        try {
            // التحقق من صحة البيانات القادمة من المتصفح
            $credential = WebAuthn::validateAttestation($request->all(), $employee->user);

            // حفظ بيانات الاعتماد الجديدة في قاعدة البيانات
            return EmployeeFingerprint::create([
                'employee_id' => $employee->id,
                'credential_id' => $credential->id,
                'public_key' => $credential->publicKey,
                'counter' => $credential->counter,
                'registered_at' => now(),
            ]);

        } catch (AttestationException $e) {
            // في حالة فشل التحقق، نرمي الخطأ ليتم التعامل معه في الـ Controller
            throw $e;
        }
    }

    /**
     * Generate options for fingerprint authentication (login/check-in).
     *
     * @param \App\Models\Employee $employee
     * @return \DarkGhostHunter\Larapass\Contracts\WebAuthn\PublicKeyRequestOptions
     */
    public function generateAuthenticationOptions(Employee $employee)
    {
        // إنشاء تحدي للتحقق من بصمة موجودة مسبقًا
        return WebAuthn::generateAssertion($employee->user);
    }

    /**
     * Verify the fingerprint authentication data.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Employee $employee
     * @return bool
     * @throws \Laragear\WebAuthn\Exceptions\AssertionException
     */
    public function verifyAuthentication(Request $request, Employee $employee): bool
    {
        try {
            // التحقق من صحة بصمة الدخول
            $credential = WebAuthn::validateAssertion($request->all(), $employee->user);

            // تحديث العداد الأمني في قاعدة البيانات لمنع هجمات إعادة التشغيل
            $fingerprintRecord = EmployeeFingerprint::where('credential_id', $credential->id)->firstOrFail();
            $fingerprintRecord->update(['counter' => $credential->counter]);
            
            return true;

        } catch (AssertionException $e) {
            // فشل التحقق
            throw $e;
        }
    }
}