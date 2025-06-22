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
    Schema::create('prescription_items', function (Blueprint $table) {
      $table->id();
      $table->foreignId('prescription_id')->constrained('prescriptions')->onDelete('cascade');
      $table->foreignId('medication_id')->nullable()->constrained('medications')->onDelete('set null');
      $table->string('medication_name_manual')->nullable()->comment('اسم الدواء يدوياً إذا لم يكن في القائمة');
      $table->string('dosage', 100);
      $table->string('frequency', 100);
      $table->string('duration', 100);
      $table->text('instructions')->nullable();
      $table->integer('quantity_prescribed')->nullable();
      $table->integer('quantity_dispensed')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('prescription_items');
  }
};
