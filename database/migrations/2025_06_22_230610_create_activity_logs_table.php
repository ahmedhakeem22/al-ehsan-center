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
    Schema::create('activity_logs', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
      $table->string('activity_type')->comment('نوع النشاط (e.g., patient_created)');
      $table->text('description');
      $table->ipAddress()->nullable();
      $table->text('user_agent')->nullable();
      $table->timestamp('log_time')->useCurrent();
      $table->string('related_model_type')->nullable();
      $table->unsignedBigInteger('related_model_id')->nullable();

      $table->index(['related_model_type', 'related_model_id'], 'idx_activity_log_related_model');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('activity_logs');
  }
};
