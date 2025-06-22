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
    Schema::create('employee_documents', function (Blueprint $table) {
      $table->id();
      $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
      $table->string('document_type', 100)->comment('مثل: سيرة ذاتية، شهادة');
      $table->string('file_path');
      $table->string('file_name')->nullable();
      $table->text('description')->nullable();
      $table->foreignId('uploaded_by_user_id')->constrained('users')->onDelete('restrict');
      $table->dateTime('uploaded_at')->useCurrent();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('employee_documents');
  }
};
