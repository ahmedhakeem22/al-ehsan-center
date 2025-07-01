<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\AttendanceController;
use App\Http\Controllers\API\AttendanceRequestController;

Route::prefix('v1')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login']);

    // QR Code verification route is public but signed
    Route::get('/attendance/verify-qr/{token}', [AttendanceRequestController::class, 'verifyQrCodeAndCheckIn'])
        ->name('api.attendance.verify-qr');

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/user', function (Request $request) {
            return $request->user()->load('employeeRecord');
        });

        // Attendance (Fingerprint Flow)
        Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn']);
        Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut']);
        Route::get('/attendance/status', [AttendanceController::class, 'getAttendanceStatus']);

        // Attendance Requests (QR Code Flow)
        Route::post('/attendance-requests', [AttendanceRequestController::class, 'store']);
    });
});