<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\EmployeeShift;
use App\Models\Employee;
use App\Models\ShiftDefinition;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class EmployeeShiftController extends Controller
{
    // For calendar view (FullCalendar or similar)
    public function calendarView(Request $request)
    {
        $employees = Employee::whereHas('user', fn($q) => $q->where('is_active', true))
                                ->orderBy('full_name')->pluck('full_name', 'id');
        $shiftDefinitions = ShiftDefinition::orderBy('name')->get(); // For legend or filters

        return view('hr.employee_shifts.calendar', compact('employees', 'shiftDefinitions'));
    }

    // API endpoint to fetch shifts for the calendar
    public function getShiftsApi(Request $request)
    {
        $request->validate([
            'start' => 'required|date_format:Y-m-d',
            'end' => 'required|date_format:Y-m-d|after_or_equal:start',
            'employee_id' => 'nullable|exists:employees,id',
        ]);

        $query = EmployeeShift::with(['employee:id,full_name', 'definition:id,name,color_code,start_time,end_time'])
                              ->whereBetween('shift_date', [$request->start, $request->end]);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $shifts = $query->get()->map(function ($shift) {
            // Format for FullCalendar
            $startDateTime = Carbon::parse($shift->shift_date . ' ' . $shift->definition->start_time);
            $endDateTime = Carbon::parse($shift->shift_date . ' ' . $shift->definition->end_time);
            if ($endDateTime <= $startDateTime) { // Handle overnight shifts
                $endDateTime->addDay();
            }

            return [
                'id' => $shift->id,
                'title' => $shift->employee->full_name . ' - ' . $shift->definition->name,
                'start' => $startDateTime->toIso8601String(),
                'end' => $endDateTime->toIso8601String(),
                'backgroundColor' => $shift->definition->color_code,
                'borderColor' => $shift->definition->color_code,
                'extendedProps' => [
                    'employee_id' => $shift->employee_id,
                    'shift_definition_id' => $shift->shift_definition_id,
                    'notes' => $shift->notes,
                ]
            ];
        });
        return response()->json($shifts);
    }

    // CRUD operations (can be modal-driven from calendar or separate pages)
    public function index(Request $request) // List view for shifts
    {
        $query = EmployeeShift::with(['employee', 'definition', 'assigner'])->latest('shift_date');

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->filled('shift_definition_id')) {
            $query->where('shift_definition_id', $request->shift_definition_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('shift_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('shift_date', '<=', $request->date_to);
        }

        $employeeShifts = $query->paginate(20)->withQueryString();
        $employees = Employee::orderBy('full_name')->pluck('full_name', 'id');
        $shiftDefinitions = ShiftDefinition::orderBy('name')->pluck('name', 'id');

        return view('hr.employee_shifts.index', compact('employeeShifts', 'employees', 'shiftDefinitions'));
    }


    public function store(Request $request) // Can be called via AJAX from calendar
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'shift_definition_id' => 'required|exists:shift_definitions,id',
            'shift_date' => [
                'required',
                'date_format:Y-m-d',
                Rule::unique('employee_shifts')->where(function ($query) use ($request) {
                    return $query->where('employee_id', $request->employee_id)
                                 ->where('shift_date', $request->shift_date);
                })
            ],
            'notes' => 'nullable|string',
        ]);

        $shift = EmployeeShift::create([
            'employee_id' => $request->employee_id,
            'shift_definition_id' => $request->shift_definition_id,
            'shift_date' => $request->shift_date,
            'assigned_by_user_id' => auth()->id(),
            'notes' => $request->notes,
        ]);

        if ($request->ajax()) {
            $shift->load(['employee:id,full_name', 'definition:id,name,color_code,start_time,end_time']);
             $startDateTime = Carbon::parse($shift->shift_date . ' ' . $shift->definition->start_time);
             $endDateTime = Carbon::parse($shift->shift_date . ' ' . $shift->definition->end_time);
             if ($endDateTime <= $startDateTime) { $endDateTime->addDay(); }
            return response()->json([
                'success' => true,
                'message' => 'Shift assigned successfully.',
                'event' => [
                    'id' => $shift->id,
                    'title' => $shift->employee->full_name . ' - ' . $shift->definition->name,
                    'start' => $startDateTime->toIso8601String(),
                    'end' => $endDateTime->toIso8601String(),
                    'backgroundColor' => $shift->definition->color_code,
                    'borderColor' => $shift->definition->color_code,
                     'extendedProps' => [
                        'employee_id' => $shift->employee_id,
                        'shift_definition_id' => $shift->shift_definition_id,
                        'notes' => $shift->notes,
                    ]
                ]
            ]);
        }
        return redirect()->route('hr.employee_shifts.index')->with('success', 'Shift assigned successfully.');
    }

    public function update(Request $request, EmployeeShift $employeeShift) // Can be called via AJAX (e.g., drag-drop or edit modal)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'shift_definition_id' => 'required|exists:shift_definitions,id',
            'shift_date' => [
                'required',
                'date_format:Y-m-d',
                 Rule::unique('employee_shifts')->where(function ($query) use ($request) {
                    return $query->where('employee_id', $request->employee_id)
                                 ->where('shift_date', $request->shift_date);
                })->ignore($employeeShift->id)
            ],
            'notes' => 'nullable|string',
        ]);

        $employeeShift->update([
            'employee_id' => $request->employee_id,
            'shift_definition_id' => $request->shift_definition_id,
            'shift_date' => $request->shift_date,
            'notes' => $request->notes,
            // 'assigned_by_user_id' => auth()->id(), // Update if a different user modifies
        ]);

        if ($request->ajax()) {
            $employeeShift->load(['employee:id,full_name', 'definition:id,name,color_code,start_time,end_time']);
             $startDateTime = Carbon::parse($employeeShift->shift_date . ' ' . $employeeShift->definition->start_time);
             $endDateTime = Carbon::parse($employeeShift->shift_date . ' ' . $employeeShift->definition->end_time);
             if ($endDateTime <= $startDateTime) { $endDateTime->addDay(); }
            return response()->json([
                'success' => true,
                'message' => 'Shift updated successfully.',
                'event' => [
                    'id' => $employeeShift->id,
                    'title' => $employeeShift->employee->full_name . ' - ' . $employeeShift->definition->name,
                    'start' => $startDateTime->toIso8601String(),
                    'end' => $endDateTime->toIso8601String(),
                    'backgroundColor' => $employeeShift->definition->color_code,
                    'borderColor' => $employeeShift->definition->color_code,
                     'extendedProps' => [
                        'employee_id' => $employeeShift->employee_id,
                        'shift_definition_id' => $employeeShift->shift_definition_id,
                        'notes' => $employeeShift->notes,
                    ]
                ]
            ]);
        }
        return redirect()->route('hr.employee_shifts.index')->with('success', 'Shift updated successfully.');
    }

    public function destroy(Request $request, EmployeeShift $employeeShift) // Can be called via AJAX
    {
        $employeeShift->delete();
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Shift deleted successfully.']);
        }
        return redirect()->route('hr.employee_shifts.index')->with('success', 'Shift deleted successfully.');
    }
}