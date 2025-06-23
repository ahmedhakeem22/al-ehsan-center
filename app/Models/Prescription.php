<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;

    protected $table = 'prescriptions';

    protected $fillable = [
        'treatment_plan_id',
        'patient_id',
        'doctor_id',
        'pharmacist_id',
        'prescription_date',
        'dispensing_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'prescription_date' => 'datetime',
        'dispensing_date' => 'datetime',
    ];

    public function treatmentPlan()
    {
        return $this->belongsTo(TreatmentPlan::class, 'treatment_plan_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function pharmacist()
    {
        return $this->belongsTo(User::class, 'pharmacist_id');
    }

    public function items()
    {
        return $this->hasMany(PrescriptionItem::class, 'prescription_id');
    }
}