<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->integer('age')->nullable();
            $table->string('phone_number', 20)->nullable()->unique();
            $table->string('qualification')->nullable();
            $table->string('marital_status', 50)->nullable();
            $table->decimal('salary', 10, 2)->nullable();
            $table->string('job_title', 100);
            $table->text('address')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->date('joining_date')->nullable();
            $table->string('profile_picture_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};