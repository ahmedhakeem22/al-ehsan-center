<?php

namespace App\Enums;

trait HasEnumLabels
{
    public static function labels(): array
    {
        return array_reduce(self::cases(), function ($carry, $case) {
            $carry[$case->value] = $case->label();
            return $carry;
        }, []);
    }
}

enum ClinicalNoteTypeEnum: string
{
    use HasEnumLabels;

    case DOCTOR_RECOMMENDATION = 'doctor_recommendation';
    case NURSE_OBSERVATION = 'nurse_observation';
    case PSYCHOLOGIST_NOTE = 'psychologist_note';
    case DAILY_VISIT_NOTE = 'daily_visit_note';

    public function label(): string
    {
        return match ($this) {
            self::DOCTOR_RECOMMENDATION => 'توصية طبيب',
            self::NURSE_OBSERVATION => 'ملاحظة تمريض',
            self::PSYCHOLOGIST_NOTE => 'ملاحظة نفسية',
            self::DAILY_VISIT_NOTE => 'ملاحظة زيارة يومية',
        };
    }
}