<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrescriptionItem extends Model
{
    use HasFactory;

    protected $table = 'prescription_items';

    protected $fillable = [
        'prescription_id',
        'medication_id',
        'medication_name_manual',
        'dosage',
        'frequency',
        'duration',
        'instructions',
        'quantity_prescribed',
        'quantity_dispensed',
    ];

    protected $casts = [
        'quantity_prescribed' => 'integer',
        'quantity_dispensed' => 'integer',
    ];

    public function prescription()
    {
        return $this->belongsTo(Prescription::class, 'prescription_id');
    }

    public function medication()
    {
        return $this->belongsTo(Medication::class, 'medication_id');
    }
}