<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftDefinition extends Model
{
    use HasFactory;

    protected $table = 'shift_definitions';

    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'duration_hours',
        'color_code',
    ];

    protected $casts = [
        'duration_hours' => 'decimal:2',
    ];

    public function employeeShifts()
    {
        return $this->hasMany(EmployeeShift::class, 'shift_definition_id');
    }
}