<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
          
            $table->enum('gender', ['male', 'female', 'other', 'unknown'])->nullable()->after('approximate_age');
          
            // $table->string('gender', 10)->nullable()->after('approximate_age');
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn('gender');
        });
    }
};