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
    Schema::create('clinical_notes', function (Blueprint $table) {
      $table->id();
      $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
      $table->foreignId('author_id')->constrained('users')->onDelete('restrict');
      $table->string('author_role', 50)->comment('دور كاتب الملاحظة لتسهيل الفلترة');
      $table->enum('note_type', ['doctor_recommendation', 'nurse_observation', 'psychologist_note', 'daily_visit_note']);
      $table->text('content');
      $table->boolean('is_actioned')->default(false);
      $table->foreignId('actioned_by_user_id')->nullable()->constrained('users')->onDelete('set null');
      $table->dateTime('actioned_at')->nullable();
      $table->text('action_notes')->nullable();
      $table->foreignId('related_to_note_id')->nullable()->constrained('clinical_notes')->onDelete('set null');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('clinical_notes');
  }
};
