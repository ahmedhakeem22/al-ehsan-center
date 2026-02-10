<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeFingerprint extends Model
{
    use HasFactory;
    
    protected $table = 'employee_fingerprints';

    protected $fillable = [
        'employee_id',
        'credential_id',
        'public_key',
        'counter',
        'is_active',
        'registered_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'registered_at' => 'datetime',
    ];

    /**
     * Get the employee that owns the fingerprint.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}