<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Model::unguard();
        Schema::disableForeignKeyConstraints();

        $this->call([
            RoleSeeder::class,
            // PermissionSeeder::class, // قم بإلغاء التعليق إذا أنشأته
            FloorSeeder::class,
            AssessmentItemSeeder::class,
          
        ]);

        // 2. بيانات تعتمد على ما سبق
        $this->call([
            RoomAndBedSeeder::class, // يعتمد على Floors
        ]);
        
        // 4. بيانات الجدولة
        // $this->call(EmployeeShiftSeeder::class); // قم بإلغاء التعليق إذا أنشأته

        Schema::enableForeignKeyConstraints();
        Model::reguard();
        
    }
}