<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BiometricDeviceController extends Controller
{
  /**
   * Store attendance data from biometric device.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    // Basic validation
    $request->validate([
      'device_id' => 'required|string',
      'attendance_logs' => 'required|array',
      'attendance_logs.*.user_id' => 'required', // Employee ID on device
      'attendance_logs.*.timestamp' => 'required|date',
      'attendance_logs.*.status' => 'nullable|in:0,1,255', // 0: Check-in, 1: Check-out (Common ZKTeco standards)
    ]);

    Log::info('Biometric push received:', $request->all());

    $logs = $request->input('attendance_logs');
    $savedCount = 0;
    $errors = [];

    foreach ($logs as $log) {
      try {
        // Look up employee by biometric_id (the ID from the device)
        $employee = Employee::where('biometric_id', $log['user_id'])->first();

        if (!$employee) {
          $errors[] = "Employee not found for Biometric ID: {$log['user_id']}";
          continue;
        }

        $timestamp = Carbon::parse($log['timestamp']);
        $date = $timestamp->toDateString();

        // Find existing attendance for this day
        $attendance = Attendance::where('employee_id', $employee->id)
          ->whereDate('check_in_time', $date)
          ->first();

        // Determine Check-in or Check-out
        // If device sends status: 0 = CheckIn, 1 = CheckOut
        $deviceStatus = $log['status'] ?? null;

        if (!$attendance) {
          // No record for today, create Check-in
          // Verify it's not a Check-out punch (unless logic allows)
          if ($deviceStatus === 1) {
            // It's a checkout punch but no checkin exists. 
            // Strategy: Create record with check-in as this time (late arrival?) or ignore?
            // For now, let's create it as Check-in but log a note.
            Attendance::create([
              'employee_id' => $employee->id,
              'check_in_time' => $timestamp,
              'notes' => 'First punch was Check-Out status from device',
            ]);
          } else {
            Attendance::create([
              'employee_id' => $employee->id,
              'check_in_time' => $timestamp,
            ]);
          }
          $savedCount++;
        } else {
          // Record exists. Update Check-out if this punch is later than check-in
          // Logic: Always update check-out to the LATEST punch of the day
          if ($timestamp->gt($attendance->check_in_time)) {
            if (!$attendance->check_out_time || $timestamp->gt($attendance->check_out_time)) {
              $attendance->update([
                'check_out_time' => $timestamp,
              ]);
              $savedCount++;
            }
          }
        }
      } catch (\Exception $e) {
        Log::error("Error processing log: " . $e->getMessage());
        $errors[] = "Error for ID {$log['user_id']}: {$e->getMessage()}";
      }
    }

    return response()->json([
      'status' => 'success',
      'message' => "Processed {$savedCount} logs.",
      'errors' => $errors
    ]);
  }
}
