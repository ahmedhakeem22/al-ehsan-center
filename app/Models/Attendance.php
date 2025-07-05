<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'check_in_time',
        'check_in_ip_address',
        'check_out_time',
        'check_out_ip_address',
        'notes',
    ];

    protected $casts = [
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

      
    public function shift()
{
    // الطريقة الصحيحة: نقوم بالفلترة بناءً على التاريخ الفعلي لسجل الحضور الحالي
    return $this->hasOne(EmployeeShift::class, 'employee_id', 'employee_id')
                ->where('shift_date', $this->check_in_time->toDateString());
}

    /**
     * حساب مدة العمل الفعلية بالساعات (رقم عشري).
     * @return float|null
     */
    public function getWorkedHoursAttribute(): ?float
    {
        if ($this->check_in_time && $this->check_out_time) {
            // إرجاع المدة بالساعات مع تقريب لأقرب منزلتين عشريتين
            return round($this->check_out_time->diffInMinutes($this->check_in_time) / 60, 2);
        }
        return null;
    }

    /**
     * عرض مدة العمل كنص منسق (مثال: "8h 15m").
     * @return string|null
     */
    public function getFormattedWorkedHoursAttribute(): ?string
    {
        if ($this->check_in_time && $this->check_out_time) {
            $interval = $this->check_in_time->diff($this->check_out_time);
            return $interval->format('%h') . 'h ' . $interval->format('%i') . 'm';
        }
        return 'غير مكتمل';
    }

    /**
     * التحقق مما إذا كان الموظف قد حضر متأخراً.
     * @param int $gracePeriodInMinutes فترة السماح بالدقائق قبل اعتباره متأخراً.
     * @return bool|null - true إذا كان متأخر، false إذا كان في الوقت، null إذا لم توجد مناوبة.
     */
    public function isLate(int $gracePeriodInMinutes = 15): ?bool
    {
        if (!$this->check_in_time) {
            return null; // لم يسجل حضور بعد
        }

        // استخدام العلاقة shift التي عرفناها لجلب المناوبة وتعريفها
        $shift = $this->relationLoaded('shift') ? $this->shift : $this->shift()->with('definition')->first();

        if (!$shift || !$shift->definition) {
            return null; // لا توجد مناوبة مجدولة لهذا اليوم، لا يمكن تحديد التأخير
        }

        $scheduledStartTime = Carbon::parse($shift->shift_date->toDateString() . ' ' . $shift->definition->start_time);
        $allowedCheckInTime = $scheduledStartTime->addMinutes($gracePeriodInMinutes);

        return $this->check_in_time->isAfter($allowedCheckInTime);
    }

}