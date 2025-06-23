<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabTestRequest extends Model
{
    use HasFactory;

    protected $table = 'lab_test_requests';

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'lab_technician_id',
        'request_date',
        'result_date',
        'status',
        'notes_from_doctor',
        'notes_from_lab',
    ];

    protected $casts = [
        'request_date' => 'datetime',
        'result_date' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function labTechnician()
    {
        return $this->belongsTo(User::class, 'lab_technician_id');
    }

    public function items()
    {
        return $this->hasMany(RequestedLabTestItem::class, 'lab_test_request_id');
    }
}