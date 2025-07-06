<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
            'device_id' => 'required|string',
        ]);

        $loginField = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        
        if (!Auth::attempt([$loginField => $credentials['login'], 'password' => $credentials['password']])) {
            return response()->json(['message' => 'بيانات الدخول غير صحيحة.'], 401);
        }

        $user = User::where($loginField, $credentials['login'])->first();

        if (!$user->is_active || !$user->employeeRecord) {
            return response()->json(['message' => 'الحساب غير مفعل أو غير مرتبط بملف موظف.'], 403);
        }

        // ---  بداية التعديل على منطق ربط الجهاز ---

        // 1. إذا كان المستخدم يمتلك جهازًا مسجلاً بالفعل
        if ($user->device_id) {
            // تحقق مما إذا كان يحاول الدخول من جهاز مختلف
            if ($user->device_id !== $request->device_id) {
                return response()->json(['message' => 'هذا الحساب مرتبط بجهاز آخر. يرجى الاتصال بالدعم الفني.'], 403);
            }
        } 
        // 2. إذا كان المستخدم لا يمتلك جهازًا مسجلاً (device_id is null)
        else {
            // <<< الإضافة الجديدة والأهم >>>
            // تحقق مما إذا كان الجهاز القادم مرتبطًا بالفعل بمستخدم آخر
            $existingDeviceUser = User::where('device_id', $request->device_id)->first();
            
            if ($existingDeviceUser) {
                // نعم، الجهاز مرتبط بمستخدم آخر. امنع تسجيل الدخول.
                return response()->json(['message' => 'هذا الجهاز مرتبط بالفعل بحساب آخر. لا يمكن استخدامه.'], 403);
            }

            // إذا وصلنا هنا، فهذا يعني أن الجهاز غير مرتبط بأي شخص. يمكننا ربطه الآن.
            $user->device_id = $request->device_id;
            $user->save();
        }

        // --- نهاية التعديل على منطق ربط الجهاز ---

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => $user->load('employeeRecord'),
            'token' => $token
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'تم تسجيل الخروج بنجاح.']);
    }
}