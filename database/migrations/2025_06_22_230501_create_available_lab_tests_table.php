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
    Schema::create('available_lab_tests', function (Blueprint $table) {
      $table->id();
      $table->string('name')->unique();
      $table->string('code', 50)->nullable()->unique();
      $table->text('description')->nullable();
      $table->text('reference_range')->nullable();
      $table->decimal('cost', 8, 2)->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('available_lab_tests');
  }
};
