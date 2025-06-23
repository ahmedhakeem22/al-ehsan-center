<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FunctionalAssessmentResponse extends Model
{
    use HasFactory;

    protected $table = 'functional_assessment_responses';

    protected $fillable = [
        'assessment_form_id',
        'assessment_item_id',
        'rating',
    ];

    public function assessmentForm()
    {
        return $this->belongsTo(FunctionalAssessmentForm::class, 'assessment_form_id');
    }

  
    public function item()
    {
        return $this->belongsTo(AssessmentItem::class, 'assessment_item_id');
    }
}