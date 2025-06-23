<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Floor;

class FloorSeeder extends Seeder
{
    public function run(): void
    {
        $floors = [
            ['name' => 'الطابق الأول: رعاية مركزة', 'description' => 'للحالات الشديدة والجديدة'],
            ['name' => 'الطابق الثاني: تأهيل متوسط', 'description' => 'للحالات التي أظهرت تحسناً'],
            ['name' => 'الطابق الثالث: تأهيل متقدم', 'description' => 'للحالات المستقرة والمقبلة على الخروج'],
        ];

        foreach ($floors as $floor) {
            Floor::updateOrCreate(['name' => $floor['name']], $floor);
        }
    }
}