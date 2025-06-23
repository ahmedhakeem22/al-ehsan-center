<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FunctionalAssessmentForm extends Model
{
    use HasFactory;

    protected $table = 'functional_assessment_forms';

    protected $fillable = [
        'patient_id',
        'assessor_id',
        'assessment_date_hijri',
        'assessment_date_gregorian',
        'recommended_stay_duration',
        'medication_axis_average',
        'psychological_axis_average',
        'activities_axis_average',
        'overall_improvement_percentage',
        'notes',
    ];

    protected $casts = [
        'assessment_date_gregorian' => 'date',
        'medication_axis_average' => 'decimal:2',
        'psychological_axis_average' => 'decimal:2',
        'activities_axis_average' => 'decimal:2',
        'overall_improvement_percentage' => 'decimal:2',
    ];

    
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

  
    public function assessor()
    {
        return $this->belongsTo(User::class, 'assessor_id');
    }

    
    public function responses()
    {
        return $this->hasMany(FunctionalAssessmentResponse::class, 'assessment_form_id');
    }
}