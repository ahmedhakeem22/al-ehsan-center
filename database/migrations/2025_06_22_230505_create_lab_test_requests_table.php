<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('lab_test_requests', function (Blueprint $table) {
      $table->id();
      $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
      $table->foreignId('doctor_id')->constrained('users')->onDelete('restrict');
      $table->foreignId('lab_technician_id')->nullable()->constrained('users')->onDelete('set null');
      $table->dateTime('request_date');
      $table->dateTime('result_date')->nullable();
      $table->enum('status', ['pending_sample', 'sample_collected', 'processing', 'completed', 'cancelled'])->default('pending_sample');
      $table->text('notes_from_doctor')->nullable();
      $table->text('notes_from_lab')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('lab_test_requests');
  }
};
