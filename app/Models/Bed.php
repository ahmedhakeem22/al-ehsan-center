<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bed extends Model
{
    use HasFactory;

    protected $table = 'beds';

    protected $fillable = [
        'room_id',
        'bed_number',
        'status',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function patient()
    {
        return $this->hasOne(Patient::class, 'current_bed_id');
    }
}