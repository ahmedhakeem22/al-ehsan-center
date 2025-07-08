<?php

namespace App\Models;

use App\Enums\ClinicalNoteTypeEnum; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicalNote extends Model
{
    use HasFactory;

    protected $table = 'clinical_notes';

    protected $fillable = [
        'patient_id',
        'author_id',
        'author_role',
        'note_type',
        'content',
        'is_actioned',
        'actioned_by_user_id',
        'actioned_at',
        'action_notes',
        'related_to_note_id',
    ];

    protected $casts = [
        'note_type' => ClinicalNoteTypeEnum::class,
        'is_actioned' => 'boolean',
        'actioned_at' => 'datetime',
    ];

    
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

      public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

        public function actionedBy()
    {
        return $this->belongsTo(User::class, 'actioned_by_user_id');
    }
    
        public function parentNote()
    {
        return $this->belongsTo(ClinicalNote::class, 'related_to_note_id');
    }
    
    public function replies()
    {
        return $this->hasMany(ClinicalNote::class, 'related_to_note_id');
    }
}