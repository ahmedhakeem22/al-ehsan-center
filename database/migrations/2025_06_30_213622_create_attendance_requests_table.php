<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            // 'check_in' or 'check_out'
            $table->enum('request_type', ['check_in', 'check_out']);
            // 'pending', 'approved', 'expired'
            $table->enum('status', ['pending', 'approved', 'expired'])->default('pending');
            $table->string('token')->unique()->nullable(); // To generate the QR code
            $table->timestamp('expires_at')->nullable(); // QR code expiry
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_requests');
    }
};