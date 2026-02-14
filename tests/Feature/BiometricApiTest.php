<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Employee;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class BiometricApiTest extends TestCase
{
  // use RefreshDatabase; // Be careful using this if you don't want to wipe the local DB. Using manual cleanup or separate test DB is safer.

  public function test_biometric_push_creates_attendance()
  {
    // 1. Create an employee with a biometric_id
    $biometricId = '99999';
    $employee = Employee::create([
      'full_name' => 'Test Employee',
      'job_title' => 'Tester',
      'biometric_id' => $biometricId,
    ]);

    // 2. Prepare payload
    $timestamp = Carbon::now()->format('Y-m-d H:i:s');
    $payload = [
      'device_id' => 'TEST_DEVICE_001',
      'attendance_logs' => [
        [
          'user_id' => $biometricId,
          'timestamp' => $timestamp,
          'status' => 0, // Check-in
        ]
      ]
    ];

    // 3. Send Request
    $response = $this->postJson('/api/v1/attendance/biometric/push', $payload);

    // 4. Assert Response
    $response->assertStatus(200)
      ->assertJson(['status' => 'success']);

    // 5. Assert Database
    $this->assertDatabaseHas('attendances', [
      'employee_id' => $employee->id,
      'check_in_time' => $timestamp,
    ]);

    // Cleanup
    Attendance::where('employee_id', $employee->id)->delete();
    $employee->delete();
  }
}
