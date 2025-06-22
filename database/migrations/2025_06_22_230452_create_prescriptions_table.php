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
    Schema::create('prescriptions', function (Blueprint $table) {
      $table->id();
      $table->foreignId('treatment_plan_id')->nullable()->constrained('treatment_plans')->onDelete('set null');
      $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
      $table->foreignId('doctor_id')->constrained('users')->onDelete('restrict');
      $table->foreignId('pharmacist_id')->nullable()->constrained('users')->onDelete('set null');
      $table->dateTime('prescription_date');
      $table->dateTime('dispensing_date')->nullable();
      $table->enum('status', ['pending', 'dispensed', 'cancelled'])->default('pending');
      $table->text('notes')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('prescriptions');
  }
};
