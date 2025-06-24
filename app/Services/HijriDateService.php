<?php

namespace App\Services;

use Kindy\HijriDate\HijriDateTime; // استيراد الكلاس الرئيسي من المكتبة الجديدة
use Carbon\Carbon;

class HijriDateService
{
    /**
     * Convert Gregorian date to Hijri date string.
     *
     * @param int $gregorianYear
     * @param int $gregorianMonth
     * @param int $gregorianDay
     * @return string Hijri date in '_Y-_m-_d' format for Kindy (or Y-m-d after formatting)
     */
    public function gregorianToHijri(int $gregorianYear, int $gregorianMonth, int $gregorianDay): string
    {
        // إنشاء كائن HijriDateTime من التاريخ الميلادي
        // نمرر التاريخ الميلادي كـ timestamp أو سلسلة تاريخ يفهمها strtotime
        $gregorianDateString = "$gregorianYear-$gregorianMonth-$gregorianDay";
        $hijriDateTime = new HijriDateTime($gregorianDateString);

        // التنسيق باستخدام بادئة الشرطة السفلية للحقول الهجرية
        return $hijriDateTime->format('_Y-_m-_d');
    }

    /**
     * Convert Hijri date to Gregorian date object.
     *
     * @param int $hijriYear
     * @param int $hijriMonth
     * @param int $hijriDay
     * @return Carbon|null
     */
    public function hijriToGregorian(int $hijriYear, int $hijriMonth, int $hijriDay): ?Carbon
    {
        try {
            // إنشاء كائن HijriDateTime من التاريخ الهجري
            $hijriDateTime = HijriDateTime::createFromHijri($hijriYear, $hijriMonth, $hijriDay);

            // الحصول على التاريخ الميلادي كـ timestamp ثم تحويله إلى Carbon
            // أو استخدام دوال format للحصول على مكونات التاريخ الميلادي
            $gregorianTimestamp = $hijriDateTime->getTimestamp();
            return Carbon::createFromTimestamp($gregorianTimestamp, $hijriDateTime->getTimezone());

            // طريقة أخرى إذا أردت الحصول على المكونات
            // $year = (int)$hijriDateTime->format('Y');
            // $month = (int)$hijriDateTime->format('m');
            // $day = (int)$hijriDateTime->format('d');
            // return Carbon::createFromDate($year, $month, $day, $hijriDateTime->getTimezone());

        } catch (\Exception $e) {
            // Log::error("Error converting Hijri to Gregorian: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get current Hijri date string.
     *
     * @return string Hijri date in '_Y-_m-_d' format
     */
    public function getCurrentHijriDate(): string
    {
        // إنشاء كائن HijriDateTime للوقت الحالي
        $hijriDateTime = new HijriDateTime(); // الافتراضي هو "now"
        return $hijriDateTime->format('_Y-_m-_d');
    }

    /**
     * (اختياري) للحصول على التاريخ الهجري بتنسيق معين مع أسماء الشهور العربية
     *
     * @param string $gregorianDateString (e.g., '2024-05-30')
     * @return string
     */
    public function getFormattedHijriDateAr(string $gregorianDateString): string
    {
        $hijriDateTime = new HijriDateTime($gregorianDateString, null, 'ar'); // 'ar' لتفعيل اللغة العربية
        return $hijriDateTime->format('_j _F _Y هـ'); // مثال للتنسيق
    }
}