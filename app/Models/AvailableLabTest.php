<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvailableLabTest extends Model
{
    use HasFactory;

    protected $table = 'available_lab_tests';

    protected $fillable = [
        'name',
        'code',
        'description',
        'reference_range',
        'cost',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
    ];

    public function requestedItems()
    {
        return $this->hasMany(RequestedLabTestItem::class, 'available_lab_test_id');
    }
}