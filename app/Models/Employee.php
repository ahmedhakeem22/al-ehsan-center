<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id', // أضفناه هنا
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

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'date_of_birth' => 'date',
        'joining_date' => 'date',
        'salary' => 'decimal:2',
    ];

    /**
     * Get the user account that owns the employee record.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ... بقية العلاقات (documents, shifts, attendance) تبقى كما هي ...
    public function documents()
    {
        return $this->hasMany(EmployeeDocument::class, 'employee_id');
    }

    public function shifts()
    {
        return $this->hasMany(EmployeeShift::class, 'employee_id');
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'employee_id');
    }
}