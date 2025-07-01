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
            'login' => 'required|string', // can be username or email
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

        // Device binding logic
        if ($user->device_id && $user->device_id !== $request->device_id) {
            return response()->json(['message' => 'هذا الحساب مرتبط بجهاز آخر. يرجى الاتصال بالدعم الفني.'], 403);
        }

        if (!$user->device_id) {
            $user->device_id = $request->device_id;
            $user->save();
        }

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