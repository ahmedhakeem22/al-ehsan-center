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
    Schema::create('medications', function (Blueprint $table) {
      $table->id();
      $table->string('name')->unique();
      $table->string('generic_name')->nullable();
      $table->string('manufacturer')->nullable();
      $table->string('form', 100)->nullable()->comment('مثل: قرص، شراب، حقنة');
      $table->string('strength', 100)->nullable()->comment('مثل: 50mg, 10mg/5ml');
      $table->text('notes')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('medications');
  }
};
