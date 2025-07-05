<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeShift extends Model
{
  use HasFactory;

  protected $table = 'employee_shifts';

  protected $fillable = [
    'employee_id',
    'shift_definition_id',
    'shift_date',
    'assigned_by_user_id',
    'notes',
  ];

  protected $casts = [
    'shift_date' => 'date',
  ];


  public function employee()
  {
    return $this->belongsTo(Employee::class, 'employee_id');
  }

  public function definition()
  {
    return $this->belongsTo(ShiftDefinition::class, 'shift_definition_id');
  }
  public function assigner()
  {
    return $this->belongsTo(User::class, 'assigned_by_user_id');
  }

  public function attendanceOnDate()
  {
    // الطريقة الصحيحة والأكثر أناقة باستخدام whereDate
    // يقوم بمقارنة جزء التاريخ من عمود 'check_in_time' مع تاريخ المناوبة
    return $this->hasOne(Attendance::class, 'employee_id', 'employee_id')
      ->whereDate('check_in_time', $this->shift_date);
  }
}