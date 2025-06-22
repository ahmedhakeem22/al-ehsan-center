<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_items', function (Blueprint $table) {
            $table->id();
            $table->enum('axis_type', ['medication', 'psychological', 'activities'])->comment('نوع المحور');
            $table->text('item_text_ar')->comment('نص البند باللغة العربية');
            $table->string('criteria_1_ar')->comment('معيار التقييم 1');
            $table->string('criteria_5_ar')->comment('معيار التقييم 5 (كأمثلة)');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_items');
    }
};