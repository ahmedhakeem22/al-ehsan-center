<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('functional_assessment_responses', function (Blueprint $table) {
      $table->id();
      $table->foreignId('assessment_form_id')->constrained('functional_assessment_forms')->onDelete('cascade');
      $table->foreignId('assessment_item_id')->constrained('assessment_items')->onDelete('restrict');
      $table->integer('rating');
      $table->timestamps();

      // هذا هو التعديل المهم
      $table->unique(['assessment_form_id', 'assessment_item_id'], 'form_item_unique');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('functional_assessment_responses');
  }
};