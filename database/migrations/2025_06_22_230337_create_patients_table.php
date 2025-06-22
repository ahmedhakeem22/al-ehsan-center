<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('file_number', 50)->nullable()->unique()->comment('رقم الملف (EH-YYYY-XXXX)');
            $table->string('full_name');
            $table->string('profile_image_path');
            $table->integer('approximate_age')->nullable();
            $table->string('province', 100)->nullable();
            $table->date('arrival_date');
            $table->text('condition_on_arrival')->nullable();
            $table->foreignId('current_bed_id')->nullable()->unique()->constrained('beds')->onDelete('set null');
            $table->enum('status', ['active', 'discharged', 'deceased', 'transferred'])->default('active');
            $table->foreignId('created_by_user_id')->constrained('users')->onDelete('restrict')->comment('مسؤول الاستقبال الذي أضاف المريض');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};