<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\ShiftDefinition;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ShiftDefinitionController extends Controller
{
    public function index()
    {
        $shiftDefinitions = ShiftDefinition::orderBy('name')->paginate(10);
        return view('hr.shift_definitions.index', compact('shiftDefinitions'));
    }

    public function create()
    {
        return view('hr.shift_definitions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:shift_definitions,name',
            'start_time' => 'required|date_format:H:i', // or H:i:s
            'end_time' => 'required|date_format:H:i|after:start_time', // or H:i:s
            'duration_hours' => 'required|numeric|min:0.5|max:24',
            'color_code' => 'nullable|string|max:7|regex:/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/', // Hex color
        ]);

        ShiftDefinition::create($request->all());
        return redirect()->route('hr.shift_definitions.index')->with('success', 'Shift definition created successfully.');
    }

    public function edit(ShiftDefinition $shiftDefinition)
    {
        return view('hr.shift_definitions.edit', compact('shiftDefinition'));
    }

    public function update(Request $request, ShiftDefinition $shiftDefinition)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('shift_definitions')->ignore($shiftDefinition->id)],
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'duration_hours' => 'required|numeric|min:0.5|max:24',
            'color_code' => 'nullable|string|max:7|regex:/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/',
        ]);

        $shiftDefinition->update($request->all());
        return redirect()->route('hr.shift_definitions.index')->with('success', 'Shift definition updated successfully.');
    }

    public function destroy(ShiftDefinition $shiftDefinition)
    {
        if ($shiftDefinition->employeeShifts()->count() > 0) {
            return redirect()->route('hr.shift_definitions.index')
                             ->with('error', 'Cannot delete. This shift definition is used in employee schedules.');
        }
        $shiftDefinition->delete();
        return redirect()->route('hr.shift_definitions.index')->with('success', 'Shift definition deleted successfully.');
    }
}