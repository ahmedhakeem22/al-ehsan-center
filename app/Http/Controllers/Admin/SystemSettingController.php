<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Floor;
use App\Models\Room;
use App\Models\AvailableLabTest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SystemSettingController extends Controller
{
    public function index()
    {
        return view('admin.settings.index');
    }

    public function floorsIndex()
    {
        $floors = Floor::orderBy('name')->paginate(10);
        return view('admin.settings.floors.index', compact('floors'));
    }

    public function floorsCreate()
    {
        return view('admin.settings.floors.create');
    }

    public function floorsStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:floors,name',
            'description' => 'nullable|string',
        ]);
        Floor::create($request->all());
        return redirect()->route('admin.settings.floors.index')->with('success', 'Floor created successfully.');
    }

    public function floorsEdit(Floor $floor)
    {
        return view('admin.settings.floors.edit', compact('floor'));
    }

    public function floorsUpdate(Request $request, Floor $floor)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('floors')->ignore($floor->id)],
            'description' => 'nullable|string',
        ]);
        $floor->update($request->all());
        return redirect()->route('admin.settings.floors.index')->with('success', 'Floor updated successfully.');
    }

    public function floorsDestroy(Floor $floor)
    {
        if ($floor->rooms()->count() > 0) {
            return redirect()->route('admin.settings.floors.index')->with('error', 'Cannot delete floor. It has associated rooms.');
        }
        $floor->delete();
        return redirect()->route('admin.settings.floors.index')->with('success', 'Floor deleted successfully.');
    }

    // --- Room Management ---
    public function roomsIndex()
    {
        $rooms = Room::with('floor')->orderBy('floor_id')->orderBy('room_number')->paginate(10);
        $floors = Floor::orderBy('name')->pluck('name', 'id');
        return view('admin.settings.rooms.index', compact('rooms', 'floors'));
    }

    public function roomsCreate()
    {
        $floors = Floor::orderBy('name')->pluck('name', 'id');
        return view('admin.settings.rooms.create', compact('floors'));
    }

    public function roomsStore(Request $request)
    {
        $request->validate([
            'floor_id' => 'required|exists:floors,id',
            'room_number' => ['required', 'string', 'max:50', Rule::unique('rooms')->where(function ($query) use ($request) {
                return $query->where('floor_id', $request->floor_id);
            })],
            'capacity' => 'required|integer|min:1',
        ]);
        Room::create($request->all());
        return redirect()->route('admin.settings.rooms.index')->with('success', 'Room created successfully.');
    }

    public function roomsEdit(Room $room)
    {
        $floors = Floor::orderBy('name')->pluck('name', 'id');
        return view('admin.settings.rooms.edit', compact('room', 'floors'));
    }

    public function roomsUpdate(Request $request, Room $room)
    {
        $request->validate([
            'floor_id' => 'required|exists:floors,id',
            'room_number' => ['required', 'string', 'max:50', Rule::unique('rooms')->where(function ($query) use ($request) {
                return $query->where('floor_id', $request->floor_id);
            })->ignore($room->id)],
            'capacity' => 'required|integer|min:1',
        ]);
        $room->update($request->all());
        return redirect()->route('admin.settings.rooms.index')->with('success', 'Room updated successfully.');
    }

    public function roomsDestroy(Room $room)
    {
        if ($room->beds()->where('status', 'occupied')->count() > 0) {
             return redirect()->route('admin.settings.rooms.index')->with('error', 'Cannot delete room. It has occupied beds.');
        }
        $room->beds()->delete(); 
        $room->delete();
        return redirect()->route('admin.settings.rooms.index')->with('success', 'Room and its beds deleted successfully.');
    }

    public function labTestsIndex()
    {
        $labTests = AvailableLabTest::orderBy('name')->paginate(10);
        return view('admin.settings.labtests.index', compact('labTests'));
    }

    public function labTestsCreate()
    {
        return view('admin.settings.labtests.create');
    }

    public function labTestsStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:available_lab_tests,name',
            'code' => 'nullable|string|max:50|unique:available_lab_tests,code',
            'description' => 'nullable|string',
            'reference_range' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
        ]);
        AvailableLabTest::create($request->all());
        return redirect()->route('admin.settings.labtests.index')->with('success', 'Lab Test created successfully.');
    }

    public function labTestsEdit(AvailableLabTest $labtest) // Route model binding
    {
        return view('admin.settings.labtests.edit', compact('labtest'));
    }

    public function labTestsUpdate(Request $request, AvailableLabTest $labtest)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('available_lab_tests')->ignore($labtest->id)],
            'code' => ['nullable', 'string', 'max:50', Rule::unique('available_lab_tests')->ignore($labtest->id)],
            'description' => 'nullable|string',
            'reference_range' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
        ]);
        $labtest->update($request->all());
        return redirect()->route('admin.settings.labtests.index')->with('success', 'Lab Test updated successfully.');
    }

    public function labTestsDestroy(AvailableLabTest $labtest)
    {
        if ($labtest->requestedItems()->count() > 0) {
            return redirect()->route('admin.settings.labtests.index')->with('error', 'Cannot delete lab test. It is used in requests.');
        }
        $labtest->delete();
        return redirect()->route('admin.settings.labtests.index')->with('success', 'Lab Test deleted successfully.');
    }
}