<?php

namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;
use App\Models\AvailableLabTest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AvailableLabTestController extends Controller
{
    public function index(Request $request)
    {
        // $this->authorize('viewAny', AvailableLabTest::class); // Policy for lab admin/manager
        $query = AvailableLabTest::query();

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('code', 'like', "%{$searchTerm}%");
            });
        }
        $labTests = $query->orderBy('name')->paginate(15)->withQueryString();
        return view('lab.available_tests.index', compact('labTests'));
    }

    public function create()
    {
        // $this->authorize('create', AvailableLabTest::class);
        return view('lab.available_tests.create');
    }

    public function store(Request $request)
    {
        // $this->authorize('create', AvailableLabTest::class);
        $request->validate([
            'name' => 'required|string|max:255|unique:available_lab_tests,name',
            'code' => 'nullable|string|max:50|unique:available_lab_tests,code',
            'description' => 'nullable|string',
            'reference_range' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
        ]);
        AvailableLabTest::create($request->all());
        return redirect()->route('lab.available_tests.index')->with('success', 'Lab Test created successfully.');
    }

    public function edit(AvailableLabTest $availableLabTest) // Route model binding name should match param
    {
        // $this->authorize('update', $availableLabTest);
        return view('lab.available_tests.edit', compact('availableLabTest'));
    }

    public function update(Request $request, AvailableLabTest $availableLabTest)
    {
        // $this->authorize('update', $availableLabTest);
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('available_lab_tests')->ignore($availableLabTest->id)],
            'code' => ['nullable', 'string', 'max:50', Rule::unique('available_lab_tests')->ignore($availableLabTest->id, 'code')],
            'description' => 'nullable|string',
            'reference_range' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
        ]);
        $availableLabTest->update($request->all());
        return redirect()->route('lab.available_tests.index')->with('success', 'Lab Test updated successfully.');
    }

    public function destroy(AvailableLabTest $availableLabTest)
    {
        // $this->authorize('delete', $availableLabTest);
        if ($availableLabTest->requestedItems()->count() > 0) {
            return redirect()->route('lab.available_tests.index')->with('error', 'Cannot delete lab test. It is used in patient requests.');
        }
        $availableLabTest->delete();
        return redirect()->route('lab.available_tests.index')->with('success', 'Lab Test deleted successfully.');
    }
}