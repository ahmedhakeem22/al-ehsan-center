<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $table = 'employees';

    protected $fillable = [
        'full_name',
        'age',
        'phone_number',
        'qualification',
        'marital_status',
        'salary',
        'job_title',
        'address',
        'date_of_birth',
        'joining_date',
        'profile_picture_path',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'joining_date' => 'date',
        'salary' => 'decimal:2',
    ];

  
    public function user()
    {
        return $this->belongsTo(User::class, 'employee_id', 'id');
    }

  
    public function documents()
    {
        return $this->hasMany(EmployeeDocument::class, 'employee_id');
    }

    
    public function shifts()
    {
        return $this->hasMany(EmployeeShift::class, 'employee_id');
    }
}