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
    Schema::create('treatment_plans', function (Blueprint $table) {
      $table->id();
      $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
      $table->foreignId('doctor_id')->constrained('users')->onDelete('restrict')->comment('الطبيب المسؤول عن الخطة');
      $table->text('diagnosis');
      $table->text('plan_details')->nullable();
      $table->date('start_date');
      $table->date('end_date')->nullable();
      $table->enum('status', ['active', 'completed', 'cancelled', 'on_hold'])->default('active');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('treatment_plans');
  }
};
