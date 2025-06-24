<?php

namespace App\Http\Controllers\PatientManagement;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\PatientMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str; // <--- أضف هذا السطر
use Illuminate\Http\UploadedFile;

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
            'image_files'   => 'nullable|array', // الصور اختيارية كمجموعة، لكن إذا قُدمت، يتم التحقق من كل ملف
            'image_files.*' => 'image|mimes:jpeg,png,jpg,gif|max:20480', // 20MB لكل صورة
            'video_files'   => 'nullable|array', // الفيديوهات اختيارية كمجموعة
            'video_files.*' => 'mimetypes:video/mp4,video/quicktime,video/avi,video/mpeg,video/x-msvideo|max:51200', // 50MB لكل فيديو (يمكن تعديل الحجم)
            'description'   => 'nullable|string|max:1000',
        ], [
            'image_files.*.image' => 'الملف المحدد في حقل الصور يجب أن يكون صورة.',
            'image_files.*.mimes' => 'صيغة الصورة غير مدعومة. الصيغ المسموحة: jpeg, png, jpg, gif.',
            'image_files.*.max' => 'حجم الصورة يتجاوز الحد المسموح به (20MB).',
            'video_files.*.mimetypes' => 'صيغة الفيديو غير مدعومة. الصيغ المسموحة: mp4, mov, avi, mpeg.',
            'video_files.*.max' => 'حجم الفيديو يتجاوز الحد المسموح به (50MB).',
        ]);

        $uploadedCount = 0;
        $commonDescription = $request->description; // وصف عام

        // دالة مساعدة لمعالجة الرفع
        $processUpload = function (UploadedFile $file, $mediaType, Patient $patient, $commonDescription) use (&$uploadedCount) {
            if ($file->isValid()) {
                $originalName = $file->getClientOriginalName();
                $fileName = time() . '_' . Str::random(5) . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('patient_media/' . $patient->id, $fileName, 'public');

                PatientMedia::create([
                    'patient_id' => $patient->id,
                    'uploader_id' => auth()->id(),
                    'media_type' => $mediaType,
                    'file_path' => $path,
                    'file_name' => $originalName,
                    'description' => $commonDescription,
                    'uploaded_at' => now(),
                ]);
                $uploadedCount++;
            }
        };

        // معالجة ملفات الصور
        if ($request->hasFile('image_files')) {
            foreach ($request->file('image_files') as $file) {
                if ($file instanceof UploadedFile) { // التأكد من أنه ملف صالح
                     $processUpload($file, 'image', $patient, $commonDescription);
                }
            }
        }

        // معالجة ملفات الفيديو
        if ($request->hasFile('video_files')) {
            foreach ($request->file('video_files') as $file) {
                 if ($file instanceof UploadedFile) { // التأكد من أنه ملف صالح
                     $processUpload($file, 'video', $patient, $commonDescription);
                 }
            }
        }

        if ($uploadedCount > 0) {
            return redirect()->route('patient_management.media.index', $patient->id)
                             ->with('success', "تم رفع {$uploadedCount} ملف/ملفات وسائط بنجاح.");
        } elseif (!$request->hasFile('image_files') && !$request->hasFile('video_files')) {
             return back()->withInput()->with('error', 'يرجى اختيار ملفات للرفع.');
        }


        return back()->with('error', 'فشل رفع الملفات. يرجى التحقق من الملفات والمحاولة مرة أخرى.');
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