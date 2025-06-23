<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'admin', 'description' => 'مدير النظام الكامل الصلاحيات'],
            ['name' => 'receptionist', 'description' => 'مسؤول استقبال وتسجيل المرضى'],
            ['name' => 'doctor', 'description' => 'الطبيب المسؤول عن التشخيص والعلاج'],
            ['name' => 'psychologist', 'description' => 'الأخصائي النفسي'],
            ['name' => 'nurse', 'description' => 'الممرض المسؤول عن الرعاية اليومية'],
            ['name' => 'lab_technician', 'description' => 'فني المختبر'],
            ['name' => 'pharmacist', 'description' => 'مسؤول الصيدلية'],
            ['name' => 'hr_manager', 'description' => 'مدير الموارد البشرية'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['name' => $role['name']], $role);
        }
    }
}