<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRequest;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;

class AttendanceRequestWebController extends Controller
{
  public function index()
  {
    // Get pending requests from today
    $requests = AttendanceRequest::with('employee')
      ->where('status', 'pending')
      ->whereDate('created_at', Carbon::today())
      ->latest()
      ->paginate(15);

    return view('hr.attendance_requests.index', compact('requests'));
  }

  public function generateQrCode(AttendanceRequest $request)
  {
    // Generate a unique, short-lived token for the QR code
    $token = Str::random(40);
    $expiresAt = now()->addMinutes(2); // QR code is valid for 2 minutes

    $request->update([
      'token' => $token,
      'expires_at' => $expiresAt,
    ]);


    // قم ببناء الرابط يدويًا باستخدام APP_URL من ملف .env
    // هذا يضمن أن الرابط سيحتوي دائمًا على عنوان IP الذي يمكن للهاتف الوصول إليه
    $baseUrl = config('app.url'); // This will get APP_URL from your .env file
    $urlToVerify = "{$baseUrl}/api/v1/attendance/verify-qr/{$token}";

    return view('hr.attendance_requests.show_qr', compact('request', 'urlToVerify'));
  }
}