<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with('user.role')->latest();

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('full_name', 'like', "%{$searchTerm}%")
                  ->orWhere('phone_number', 'like', "%{$searchTerm}%")
                  ->orWhere('job_title', 'like', "%{$searchTerm}%");
            });
        }
        if ($request->filled('job_title')) {
            $query->where('job_title', $request->job_title);
        }

        $employees = $query->paginate(15)->withQueryString();
        $jobTitles = Employee::distinct()->pluck('job_title')->filter()->sort();

        return view('hr.employees.index', compact('employees', 'jobTitles'));
    }

    public function create()
    {
        // قائمة بالمستخدمين الذين ليس لديهم سجل موظف مرتبط بعد (اختياري)
        $unlinkedUsers = User::whereDoesntHave('employeeRecord')->orderBy('name')->pluck('name', 'id');
        $rolesForNewUser = Role::orderBy('name')->pluck('name', 'id'); // For creating a new user linked to employee
        return view('hr.employees.create', compact('unlinkedUsers', 'rolesForNewUser'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'job_title' => 'required|string|max:100',
            'phone_number' => ['nullable', 'string', 'max:20', Rule::unique('employees')],
            'qualification' => 'nullable|string|max:255',
            'marital_status' => 'nullable|string|max:50',
            'salary' => 'nullable|numeric|min:0',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'joining_date' => 'nullable|date',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'user_id' => 'nullable|exists:users,id|unique:employees,user_id', // Link to existing user
            // Fields for creating a new user if 'user_id' is not provided
            'create_new_user' => 'sometimes|boolean',
            'username' => 'required_if:create_new_user,true|nullable|string|max:255|unique:users,username',
            'email' => 'required_if:create_new_user,true|nullable|string|email|max:255|unique:users,email',
            'password' => 'required_if:create_new_user,true|nullable|string|min:8|confirmed',
            'role_id' => 'required_if:create_new_user,true|nullable|exists:roles,id',
        ]);

        $employeeData = $request->only([
            'full_name', 'job_title', 'phone_number', 'qualification', 'marital_status',
            'salary', 'address', 'date_of_birth', 'joining_date'
        ]);

        if ($request->hasFile('profile_picture')) {
            $fileName = time() . '_' . $request->file('profile_picture')->getClientOriginalName();
            $path = $request->file('profile_picture')->storeAs('employee_profiles', $fileName, 'public');
            $employeeData['profile_picture_path'] = $path;
        }

        $userToLink = null;
        if ($request->filled('user_id')) {
            $userToLink = User::find($request->user_id);
            if ($userToLink->employeeRecord) { // Should be caught by validation unique:employees,user_id
                return back()->with('error', 'Selected user is already linked to an employee.')->withInput();
            }
            $employeeData['user_id'] = $userToLink->id;
        } elseif ($request->boolean('create_new_user')) {
            $userToLink = User::create([
                'name' => $request->full_name, // Use employee name for user name by default
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
                'is_active' => true,
            ]);
            $employeeData['user_id'] = $userToLink->id;
        }

        $employee = Employee::create($employeeData);

        return redirect()->route('hr.employees.show', $employee->id)
                         ->with('success', 'Employee created successfully.');
    }

    public function show(Employee $employee)
    {
        $employee->load('user.role', 'documents', 'shifts.definition');
        return view('hr.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        // Users not linked OR the current employee's user
        $availableUsers = User::whereDoesntHave('employeeRecord')
                                ->orWhere('id', $employee->user_id)
                                ->orderBy('name')->pluck('name', 'id');
        $roles = Role::orderBy('name')->pluck('name', 'id'); // For editing linked user's role

        return view('hr.employees.edit', compact('employee', 'availableUsers', 'roles'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'job_title' => 'required|string|max:100',
            'phone_number' => ['nullable', 'string', 'max:20', Rule::unique('employees')->ignore($employee->id)],
            'qualification' => 'nullable|string|max:255',
            'marital_status' => 'nullable|string|max:50',
            'salary' => 'nullable|numeric|min:0',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'joining_date' => 'nullable|date',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'user_id' => ['nullable', 'exists:users,id', Rule::unique('employees')->ignore($employee->id)],
            // User update fields (if user_id is present)
            'user_role_id' => 'required_with:user_id|nullable|exists:roles,id',
            'user_is_active' => 'sometimes|boolean',
            'user_new_password' => 'nullable|string|min:8|confirmed',
        ]);

        $employeeData = $request->only([
            'full_name', 'job_title', 'phone_number', 'qualification', 'marital_status',
            'salary', 'address', 'date_of_birth', 'joining_date'
        ]);

        if ($request->hasFile('profile_picture')) {
            if ($employee->profile_picture_path) {
                Storage::disk('public')->delete($employee->profile_picture_path);
            }
            $fileName = time() . '_' . $request->file('profile_picture')->getClientOriginalName();
            $path = $request->file('profile_picture')->storeAs('employee_profiles', $fileName, 'public');
            $employeeData['profile_picture_path'] = $path;
        }

        // Handle user linking/unlinking
        if ($request->filled('user_id')) {
            $employeeData['user_id'] = $request->user_id;
            // Update linked user details
            $user = User::find($request->user_id);
            if($user) {
                $userDataToUpdate = [
                    'role_id' => $request->user_role_id,
                    'is_active' => $request->boolean('user_is_active', $user->is_active),
                ];
                if($request->filled('user_new_password')) {
                    $userDataToUpdate['password'] = Hash::make($request->user_new_password);
                }
                $user->update($userDataToUpdate);
            }

        } else { // Unlink user
            $employeeData['user_id'] = null;
        }


        $employee->update($employeeData);

        return redirect()->route('hr.employees.show', $employee->id)
                         ->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        // Consider implications: active shifts, documents, linked user account
        // Option 1: Prevent deletion if active shifts or critical data exists
        // Option 2: Soft delete employee
        // Option 3: Allow deletion, but decide what to do with linked user (deactivate, delete, or keep)

        if ($employee->profile_picture_path) {
            Storage::disk('public')->delete($employee->profile_picture_path);
        }
        // Delete documents
        $employee->documents()->each(function ($doc) {
            Storage::disk('public')->delete($doc->file_path);
            $doc->delete();
        });
        // Delete shifts
        $employee->shifts()->delete();

        // Optionally deactivate or delete linked user if no longer needed
        // if ($employee->user_id) {
        //     $user = User::find($employee->user_id);
        //     // $user->update(['is_active' => false]);
        //     // or $user->delete(); // Be careful with this
        // }

        $employee->delete();

        return redirect()->route('hr.employees.index')->with('success', 'Employee deleted successfully.');
    }
}