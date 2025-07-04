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
    Schema::create('employee_shifts', function (Blueprint $table) {
      $table->id();
      $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
      $table->foreignId('shift_definition_id')->constrained('shift_definitions')->onDelete('restrict');
      $table->date('shift_date');
      $table->foreignId('assigned_by_user_id')->constrained('users')->onDelete('restrict');
      $table->text('notes')->nullable();
      $table->timestamps();

      $table->unique(['employee_id', 'shift_date']);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('employee_shifts');
  }
};
