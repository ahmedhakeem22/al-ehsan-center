<?php

namespace App\Http\Controllers\Occupancy;

use App\Http\Controllers\Controller;
use App\Models\Floor;
use App\Models\Bed;
use Illuminate\Http\Request;

class OccupancyDashboardController extends Controller
{
    public function index(Request $request)
    {
        $floors = Floor::with([
                'rooms' => function ($query) {
                    $query->orderBy('room_number');
                },
                'rooms.beds' => function ($query) {
                    $query->with('patient:id,full_name,file_number')->orderBy('bed_number');
                }
            ])
            ->orderBy('name') // Or a custom order if needed
            ->get();

        $totalBeds = Bed::count();
        $occupiedBeds = Bed::where('status', 'occupied')->count();
        $vacantBeds = $totalBeds - $occupiedBeds;
        $occupancyPercentage = ($totalBeds > 0) ? round(($occupiedBeds / $totalBeds) * 100, 2) : 0;

        $stats = [
            'totalBeds' => $totalBeds,
            'occupiedBeds' => $occupiedBeds,
            'vacantBeds' => $vacantBeds,
            'occupancyPercentage' => $occupancyPercentage,
        ];

        return view('occupancy.dashboard.index', compact('floors', 'stats'));
    }

    public function getBedDetails(Bed $bed)
    {
        // AJAX endpoint to get bed details (patient info if occupied)
        if (request()->ajax()) {
            $bed->load('patient:id,full_name,file_number,profile_image_path', 'room.floor');
            if ($bed->patient && $bed->patient->profile_image_path) {
                $bed->patient->profile_image_url = asset('storage/' . $bed->patient->profile_image_path);
            }
            return response()->json($bed);
        }
        abort(404);
    }
}