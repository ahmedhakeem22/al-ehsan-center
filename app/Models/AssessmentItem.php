<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentItem extends Model
{
    use HasFactory;

    protected $table = 'assessment_items';

    protected $fillable = [
        'axis_type',
        'item_text_ar',
        'criteria_1_ar',
        'criteria_5_ar',
        'is_active',
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
    ];
}