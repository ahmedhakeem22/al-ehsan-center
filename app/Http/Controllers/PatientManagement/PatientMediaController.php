<?php

namespace App\Http\Controllers\PatientManagement;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\PatientMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PatientMediaController extends Controller
{
    public function index(Patient $patient)
    {
        $mediaItems = $patient->media()->orderBy('uploaded_at', 'desc')->paginate(12);
        return view('patient_management.media.index', compact('patient', 'mediaItems'));
    }

    public function create(Patient $patient)
    {
        return view('patient_management.media.create', compact('patient'));
    }

    public function store(Request $request, Patient $patient)
    {
        $request->validate([
            'media_file' => 'required|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi,mpeg|max:20480', // max 20MB
            'media_type' => 'required|in:image,video',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($request->hasFile('media_file')) {
            $file = $request->file('media_file');
            $originalName = $file->getClientOriginalName();
            $fileName = time() . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();

            // Store in a patient-specific folder
            $path = $file->storeAs('patient_media/' . $patient->id, $fileName, 'public');

            PatientMedia::create([
                'patient_id' => $patient->id,
                'uploader_id' => auth()->id(),
                'media_type' => $request->media_type,
                'file_path' => $path,
                'file_name' => $originalName,
                'description' => $request->description,
                'uploaded_at' => now(),
            ]);

            return redirect()->route('patient_management.media.index', $patient->id)
                             ->with('success', 'Media uploaded successfully.');
        }

        return back()->with('error', 'File not provided or upload failed.');
    }

    public function destroy(Patient $patient, PatientMedia $medium) // 'medium' to avoid conflict
    {
        if ($medium->patient_id !== $patient->id) {
            abort(403, 'Unauthorized action.');
        }

        Storage::disk('public')->delete($medium->file_path);
        $medium->delete();

        return redirect()->route('patient_management.media.index', $patient->id)
                         ->with('success', 'Media item deleted successfully.');
    }
}