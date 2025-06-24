<?php

namespace App\Services;

use GeniusTS\HijriDate\Hijri; // إذا كنت تستخدم مكتبة GeniusTS

class HijriDateService
{
    /**
     * Convert Gregorian date to Hijri date string (YYYY-MM-DD).
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @return string
     */
    public function gregorianToHijri(int $year, int $month, int $day): string
    {
        // باستخدام مكتبة GeniusTS كمثال
        // تأكد من تثبيتها: composer require geniusts/hijri-dates
        // وإضافة: use GeniusTS\HijriDate\Date;
        // و   : use GeniusTS\HijriDate\Translations\Arabic;
        // Hijri::setDefaultAdjustment(-1); // Or whatever adjustment needed
        // return Hijri::convertToHijri($year, $month, $day)->format('Y-m-d');

        // تنفيذ بسيط جداً وغير دقيق - استخدم مكتبة!
        // هذا مجرد مثال توضيحي ولا يجب استخدامه في الإنتاج
        if ($year < 1900) return "N/A"; // Hijri calendar calculations are complex
        // For a more accurate conversion, you MUST use a dedicated library.
        // The following is a placeholder and WILL NOT be accurate.
        $jd = gregoriantojd($month, $day, $year);
        $hijri = jdtoislamic($jd); // This PHP function might not be enabled or accurate enough
        list($hMonth, $hDay, $hYear) = explode('/', $hijri[0]);
        return sprintf('%04d-%02d-%02d', $hYear, $hMonth, $hDay);
    }
}