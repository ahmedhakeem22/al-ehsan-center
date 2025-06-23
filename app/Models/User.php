<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    
    protected $table = 'users';

  
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role_id',
        'employee_id',
        'is_active',
    ];

    
    protected $hidden = [
        'password',
        'remember_token',
    ];

    
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    
    public function employee()
    {
        return $this->hasOne(Employee::class, 'id', 'employee_id');
    }
    
      public function createdPatients()
    {
        return $this->hasMany(Patient::class, 'created_by_user_id');
    }

    public function clinicalNotes()
    {
        return $this->hasMany(ClinicalNote::class, 'author_id');
    }
}