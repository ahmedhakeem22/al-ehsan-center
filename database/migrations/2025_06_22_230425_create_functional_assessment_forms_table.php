<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('functional_assessment_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('assessor_id')->constrained('users')->onDelete('restrict')->comment('الطبيب أو الأخصائي الذي قام بالتقييم');
            $table->string('assessment_date_hijri', 20);
            $table->date('assessment_date_gregorian');
            $table->string('recommended_stay_duration')->nullable();
            $table->decimal('medication_axis_average', 5, 2)->nullable();
            $table->decimal('psychological_axis_average', 5, 2)->nullable();
            $table->decimal('activities_axis_average', 5, 2)->nullable();
            $table->decimal('overall_improvement_percentage', 5, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('functional_assessment_forms');
    }
};