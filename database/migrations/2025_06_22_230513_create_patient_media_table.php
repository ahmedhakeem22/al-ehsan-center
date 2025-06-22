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
    Schema::create('patient_media', function (Blueprint $table) {
      $table->id();
      $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
      $table->foreignId('uploader_id')->constrained('users')->onDelete('restrict');
      $table->enum('media_type', ['image', 'video']);
      $table->string('file_path');
      $table->string('file_name')->nullable();
      $table->text('description')->nullable();
      $table->dateTime('uploaded_at')->useCurrent();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('patient_media');
  }
};
