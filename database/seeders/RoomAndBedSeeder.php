<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Floor;
use App\Models\Room;
use App\Models\Bed;

class RoomAndBedSeeder extends Seeder
{
    public function run(): void
    {
        $floors = Floor::all();
        foreach ($floors as $floor) {
            // إنشاء 5 غرف في كل طابق
            for ($i = 1; $i <= 5; $i++) {
                $room = Room::updateOrCreate(
                    ['floor_id' => $floor->id, 'room_number' => 'G' . $i],
                    ['capacity' => 4] // 4 أسرة في كل غرفة
                );

                // إنشاء أسرة داخل كل غرفة
                for ($j = 1; $j <= 4; $j++) {
                    Bed::updateOrCreate(
                        ['room_id' => $room->id, 'bed_number' => 'S' . $j],
                        ['status' => 'vacant']
                    );
                }
            }
        }
    }
}