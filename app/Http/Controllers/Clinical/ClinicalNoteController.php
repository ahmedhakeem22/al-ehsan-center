<?php

namespace App\Http\Controllers\Clinical;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\ClinicalNote;
use App\Models\User; // To get current user role
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClinicalNoteController extends Controller
{
    public function index(Request $request, Patient $patient)
    {
        $query = $patient->clinicalNotes()->with('author:id,name')->latest();

        if ($request->filled('author_role')) {
            $query->where('author_role', $request->author_role);
        }
        if ($request->filled('note_type')) {
            $query->where('note_type', $request->note_type);
        }

        $notes = $query->paginate(15)->withQueryString();

        // For filtering dropdowns
        $authorRoles = ClinicalNote::distinct()->pluck('author_role')->filter()->sort();
        $noteTypes = ClinicalNote::distinct()->pluck('note_type')->filter()->sort();


        return view('clinical.notes.index', compact('patient', 'notes', 'authorRoles', 'noteTypes'));
    }

    public function create(Patient $patient)
    {
        // Determine available note types based on user role (example)
        $user = Auth::user();
        $allowedNoteTypes = [];
        if ($user->role->name === 'Doctor' || $user->role->name === 'Psychologist') {
            $allowedNoteTypes = [
                'doctor_recommendation' => 'Doctor Recommendation',
                'psychologist_note' => 'Psychologist Note',
                'daily_visit_note' => 'Daily Visit Note'
            ];
        } elseif ($user->role->name === 'Nurse') {
             $allowedNoteTypes = ['nurse_observation' => 'Nurse Observation'];
        } else { // Admin or other roles might have all types or specific ones
             $allowedNoteTypes = [
                'doctor_recommendation' => 'Doctor Recommendation',
                'nurse_observation' => 'Nurse Observation',
                'psychologist_note' => 'Psychologist Note',
                'daily_visit_note' => 'Daily Visit Note'
            ];
        }


        return view('clinical.notes.create', compact('patient', 'allowedNoteTypes'));
    }

    public function store(Request $request, Patient $patient)
    {
        $user = Auth::user();
        $request->validate([
            'note_type' => 'required|string|in:doctor_recommendation,nurse_observation,psychologist_note,daily_visit_note',
            'content' => 'required|string',
            // 'related_to_note_id' => 'nullable|exists:clinical_notes,id', // For replies
        ]);

        $authorRole = strtolower($user->role->name); // Simplified, map to 'doctor', 'nurse', etc. if role names differ

        ClinicalNote::create([
            'patient_id' => $patient->id,
            'author_id' => $user->id,
            'author_role' => $authorRole,
            'note_type' => $request->note_type,
            'content' => $request->content,
            // 'related_to_note_id' => $request->related_to_note_id,
        ]);

        // Add Notification logic here if needed

        return redirect()->route('clinical.notes.index', $patient->id)
                         ->with('success', 'Clinical note added successfully.');
    }

    public function show(Patient $patient, ClinicalNote $note)
    {
        $note->load('author', 'actionedBy', 'parentNote', 'replies.author');
        if ($note->patient_id !== $patient->id) {
            abort(404);
        }
        return view('clinical.notes.show', compact('patient', 'note'));
    }

    public function edit(Patient $patient, ClinicalNote $note)
    {
        if ($note->patient_id !== $patient->id || $note->author_id !== auth()->id()) {
            // Or use a Policy: $this->authorize('update', $note);
            return redirect()->route('clinical.notes.index', $patient->id)
                             ->with('error', 'You are not authorized to edit this note.');
        }

        $user = Auth::user();
        $allowedNoteTypes = []; // Define as in create()
         if ($user->role->name === 'Doctor' || $user->role->name === 'Psychologist') {
            $allowedNoteTypes = [
                'doctor_recommendation' => 'Doctor Recommendation',
                'psychologist_note' => 'Psychologist Note',
                'daily_visit_note' => 'Daily Visit Note'
            ];
        } elseif ($user->role->name === 'Nurse') {
             $allowedNoteTypes = ['nurse_observation' => 'Nurse Observation'];
        } else {
             $allowedNoteTypes = [
                'doctor_recommendation' => 'Doctor Recommendation',
                'nurse_observation' => 'Nurse Observation',
                'psychologist_note' => 'Psychologist Note',
                'daily_visit_note' => 'Daily Visit Note'
            ];
        }

        return view('clinical.notes.edit', compact('patient', 'note', 'allowedNoteTypes'));
    }

    public function update(Request $request, Patient $patient, ClinicalNote $note)
    {
        if ($note->patient_id !== $patient->id || $note->author_id !== auth()->id()) {
            // $this->authorize('update', $note);
             return redirect()->route('clinical.notes.index', $patient->id)
                             ->with('error', 'You are not authorized to update this note.');
        }

        $request->validate([
            'note_type' => 'required|string|in:doctor_recommendation,nurse_observation,psychologist_note,daily_visit_note',
            'content' => 'required|string',
        ]);

        $note->update([
            'note_type' => $request->note_type,
            'content' => $request->content,
        ]);

        return redirect()->route('clinical.notes.show', [$patient->id, $note->id])
                         ->with('success', 'Clinical note updated successfully.');
    }

    public function destroy(Patient $patient, ClinicalNote $note)
    {
         if ($note->patient_id !== $patient->id || !auth()->user()->can('delete clinical notes')) { // Example permission check
            // $this->authorize('delete', $note);
             return redirect()->route('clinical.notes.index', $patient->id)
                             ->with('error', 'You are not authorized to delete this note.');
        }
        // Add logic for deleting replies if any, or preventing deletion if it has replies.
        $note->delete();
        return redirect()->route('clinical.notes.index', $patient->id)
                         ->with('success', 'Clinical note deleted successfully.');
    }

    public function markAsActioned(Request $request, Patient $patient, ClinicalNote $note)
    {
        // Authorization: e.g., only a nurse can mark a doctor's recommendation as actioned.
        if ($note->patient_id !== $patient->id || !in_array(auth()->user()->role->name, ['Nurse', 'Admin'])) {
             return back()->with('error', 'Unauthorized to perform this action.');
        }

        $request->validate([
            'action_notes' => 'nullable|string|max:1000',
        ]);

        $note->update([
            'is_actioned' => true,
            'actioned_by_user_id' => auth()->id(),
            'actioned_at' => now(),
            'action_notes' => $request->action_notes,
        ]);

        return redirect()->route('clinical.notes.show', [$patient->id, $note->id])
                         ->with('success', 'Note marked as actioned.');
    }
}