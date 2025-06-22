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
    Schema::create('requested_lab_test_items', function (Blueprint $table) {
      $table->id();
      $table->foreignId('lab_test_request_id')->constrained('lab_test_requests')->onDelete('cascade');
      $table->foreignId('available_lab_test_id')->constrained('available_lab_tests')->onDelete('restrict');
      $table->string('result_value')->nullable();
      $table->string('result_unit', 50)->nullable();
      $table->boolean('is_abnormal')->nullable();
      $table->text('notes')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('requested_lab_test_items');
  }
};
