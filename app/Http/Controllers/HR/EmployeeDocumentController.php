<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EmployeeDocumentController extends Controller
{
    public function index(Employee $employee)
    {
        $documents = $employee->documents()->latest('uploaded_at')->paginate(10);
        return view('hr.documents.index', compact('employee', 'documents'));
    }

    public function create(Employee $employee)
    {
        $documentTypes = ['CV', 'Certificate', 'ID Card', 'Contract', 'Other']; // Example types
        return view('hr.documents.create', compact('employee', 'documentTypes'));
    }

    public function store(Request $request, Employee $employee)
    {
        $request->validate([
            'document_file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // Max 10MB
            'document_type' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            $originalName = $file->getClientOriginalName();
            $fileName = time() . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();

            // Store in employee-specific document folder
            $path = $file->storeAs('employee_documents/' . $employee->id, $fileName, 'public');

            EmployeeDocument::create([
                'employee_id' => $employee->id,
                'document_type' => $request->document_type,
                'file_path' => $path,
                'file_name' => $originalName,
                'description' => $request->description,
                'uploaded_by_user_id' => auth()->id(),
                'uploaded_at' => now(),
            ]);

            return redirect()->route('hr.documents.index', $employee->id)
                             ->with('success', 'Document uploaded successfully.');
        }
        return back()->with('error', 'File not provided or upload failed.');
    }

    public function download(Employee $employee, EmployeeDocument $document)
    {
        if ($document->employee_id !== $employee->id) {
            abort(403);
        }
        if (!Storage::disk('public')->exists($document->file_path)) {
            return back()->with('error', 'File not found.');
        }
        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    public function destroy(Employee $employee, EmployeeDocument $document)
    {
        if ($document->employee_id !== $employee->id) {
            abort(403);
        }
        // $this->authorize('delete', $document); // Policy check

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return redirect()->route('hr.documents.index', $employee->id)
                         ->with('success', 'Document deleted successfully.');
    }
}