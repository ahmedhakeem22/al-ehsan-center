<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $table = 'rooms';

    protected $fillable = [
        'floor_id',
        'room_number',
        'capacity',
    ];

    protected $casts = [
        'capacity' => 'integer',
    ];

    public function floor()
    {
        return $this->belongsTo(Floor::class, 'floor_id');
    }

    public function beds()
    {
        return $this->hasMany(Bed::class, 'room_id');
    }
}