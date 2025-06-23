<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $table = 'patients';

    protected $fillable = [
        'file_number',
        'full_name',
        'profile_image_path',
        'approximate_age',
        'province',
        'arrival_date',
        'condition_on_arrival',
        'current_bed_id',
        'status',
        'created_by_user_id',
    ];

    protected $casts = [
        'arrival_date' => 'date',
    ];

    
    public function currentBed()
    {
        return $this->belongsTo(Bed::class, 'current_bed_id');
    }

  
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    
    public function assessments()
    {
        return $this->hasMany(FunctionalAssessmentForm::class, 'patient_id');
    }

  
    public function media()
    {
        return $this->hasMany(PatientMedia::class, 'patient_id');
    }

  
    public function clinicalNotes()
    {
        return $this->hasMany(ClinicalNote::class, 'patient_id');
    }
    
  
    public function treatmentPlans()
    {
        return $this->hasMany(TreatmentPlan::class, 'patient_id');
    }
}