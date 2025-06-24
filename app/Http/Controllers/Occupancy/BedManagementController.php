<?php

namespace App\Http\Controllers\Occupancy;

use App\Http\Controllers\Controller;
use App\Models\Bed;
use App\Models\Room;
use App\Models\Floor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BedManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Bed::with(['room.floor', 'patient:id,full_name,file_number']);

        if($request->filled('floor_id')) {
            $query->whereHas('room', function($q) use ($request) {
                $q->where('floor_id', $request->floor_id);
            });
        }
        if($request->filled('room_id')) {
            $query->where('room_id', $request->room_id);
        }
        if($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if($request->filled('bed_number')) {
            $query->where('bed_number', 'like', '%' . $request->bed_number . '%');
        }

        $beds = $query->orderBy(Floor::select('name')->whereColumn('floors.id', 'rooms.floor_id')) // Order by floor name
                      ->orderBy(Room::select('room_number')->whereColumn('rooms.id', 'beds.room_id')) // Then by room number
                      ->orderBy('bed_number')
                      ->paginate(20)->withQueryString();

        $floors = Floor::orderBy('name')->pluck('name', 'id');
        // Add rooms for dynamic filtering based on selected floor if needed via JS
        $bedStatuses = ['vacant' => 'Vacant', 'occupied' => 'Occupied', 'reserved' => 'Reserved', 'out_of_service' => 'Out of Service'];

        return view('occupancy.beds.index', compact('beds', 'floors', 'bedStatuses'));
    }

    public function create()
    {
        $rooms = Room::with('floor')->get()->mapWithKeys(function ($room) {
            return [$room->id => $room->floor->name . ' - Room ' . $room->room_number];
        })->sort();
        return view('occupancy.beds.create', compact('rooms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'bed_number' => ['required', 'string', 'max:50', Rule::unique('beds')->where(function ($query) use ($request) {
                return $query->where('room_id', $request->room_id);
            })],
            'status' => ['required', Rule::in(['vacant', 'occupied', 'reserved', 'out_of_service'])],
        ]);

        Bed::create($request->all());
        return redirect()->route('occupancy.beds.index')->with('success', 'Bed created successfully.');
    }

    public function edit(Bed $bed)
    {
        $rooms = Room::with('floor')->get()->mapWithKeys(function ($room) {
            return [$room->id => $room->floor->name . ' - Room ' . $room->room_number];
        })->sort();
        $bedStatuses = ['vacant' => 'Vacant', 'occupied' => 'Occupied', 'reserved' => 'Reserved', 'out_of_service' => 'Out of Service'];
        return view('occupancy.beds.edit', compact('bed', 'rooms', 'bedStatuses'));
    }

    public function update(Request $request, Bed $bed)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'bed_number' => ['required', 'string', 'max:50', Rule::unique('beds')->where(function ($query) use ($request) {
                return $query->where('room_id', $request->room_id);
            })->ignore($bed->id)],
            'status' => ['required', Rule::in(['vacant', 'occupied', 'reserved', 'out_of_service'])],
        ]);

        // If status is changing from 'occupied' to something else, ensure patient_id is cleared
        if ($bed->status === 'occupied' && $request->status !== 'occupied' && $bed->patient_id) {
            // This assumes you have a patient_id directly on beds table.
            // If not, you need to find the patient assigned to this bed and update their current_bed_id
            $patient = $bed->patient; // Assuming $bed->patient relationship exists
            if($patient) {
                $patient->current_bed_id = null;
                $patient->save();
            }
            // $bed->patient_id = null; // if direct link
        }

        // If status changes to 'occupied', this should ideally be handled via patient admission flow.
        // Manual change to 'occupied' here might orphan a patient or create inconsistencies
        // unless you also provide a patient_id. For now, we allow it but it's a point of caution.

        $bed->update($request->all());
        return redirect()->route('occupancy.beds.index')->with('success', 'Bed updated successfully.');
    }

    public function destroy(Bed $bed)
    {
        if ($bed->status === 'occupied') {
            return redirect()->route('occupancy.beds.index')->with('error', 'Cannot delete an occupied bed. Please discharge or transfer the patient first.');
        }
        $bed->delete();
        return redirect()->route('occupancy.beds.index')->with('success', 'Bed deleted successfully.');
    }

    // API endpoint to get rooms for a floor (for dynamic dropdowns in bed forms)
    public function getRoomsForFloor(Request $request)
    {
        $request->validate(['floor_id' => 'required|exists:floors,id']);
        $rooms = Room::where('floor_id', $request->floor_id)
                     ->orderBy('room_number')
                     ->pluck('room_number', 'id');
        return response()->json($rooms);
    }
}