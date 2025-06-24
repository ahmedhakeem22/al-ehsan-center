<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class EmployeeAndUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // استخدام Transaction لضمان تنفيذ جميع العمليات بنجاح أو التراجع عنها كلها
        DB::transaction(function () {
            // جلب جميع الأدوار مرة واحدة لتحسين الأداء
            $roles = Role::pluck('id', 'name')->all();

            // ==========================================================
            // المستخدم الأول: مدير النظام (Super Admin) - هذا هو المستخدم التجريبي
            // ==========================================================
            $adminEmployee = Employee::updateOrCreate(
                ['phone_number' => '0500000001'],
                [
                    'full_name' => 'مدير النظام', 
                    'job_title' => 'مدير تقنية المعلومات'
                ]
            );

            User::updateOrCreate(
                ['username' => 'admin'], // اسم الدخول
                [
                    'name' => $adminEmployee->full_name,
                    'email' => 'admin@admin.com',
                    'password' => Hash::make('123456'), // كلمة المرور هي: password
                    'role_id' => $roles['admin'], // ربطه بدور 'admin'
                    'employee_id' => $adminEmployee->id,
                    'is_active' => true,
                ]
            );

            // ==========================================================
            // المستخدم الثاني: طبيب (كمثال)
            // ==========================================================
            $doctorEmployee = Employee::updateOrCreate(
                ['phone_number' => '0500000002'],
                [
                    'full_name' => 'د. خالد الأحمدي', 
                    'job_title' => 'طبيب نفسي'
                ]
            );
            User::updateOrCreate(
                ['username' => 'doctor.khaled'],
                [
                    'name' => $doctorEmployee->full_name,
                    'email' => 'doctor.khaled@ridaa.com',
                    'password' => Hash::make('password'),
                    'role_id' => $roles['doctor'], // ربطه بدور 'doctor'
                    'employee_id' => $doctorEmployee->id,
                    'is_active' => true,
                ]
            );
            
            // ==========================================================
            // المستخدم الثالث: ممرض (كمثال)
            // ==========================================================
            $nurseEmployee = Employee::updateOrCreate(
                ['phone_number' => '0500000003'],
                [
                    'full_name' => 'سالم الغامدي', 
                    'job_title' => 'رئيس التمريض'
                ]
            );
            User::updateOrCreate(
                ['username' => 'nurse.salem'],
                [
                    'name' => $nurseEmployee->full_name,
                    'email' => 'nurse.salem@ridaa.com',
                    'password' => Hash::make('password'),
                    'role_id' => $roles['nurse'], // ربطه بدور 'nurse'
                    'employee_id' => $nurseEmployee->id,
                    'is_active' => true,
                ]
            );
        });
    }
}