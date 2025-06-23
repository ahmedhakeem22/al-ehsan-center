<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    use HasFactory;

    protected $table = 'medications';

    protected $fillable = [
        'name',
        'generic_name',
        'manufacturer',
        'form',
        'strength',
        'notes',
    ];

    public function prescriptionItems()
    {
        return $this->hasMany(PrescriptionItem::class, 'medication_id');
    }
}