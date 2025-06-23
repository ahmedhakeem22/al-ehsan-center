<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestedLabTestItem extends Model
{
    use HasFactory;

    protected $table = 'requested_lab_test_items';

    protected $fillable = [
        'lab_test_request_id',
        'available_lab_test_id',
        'result_value',
        'result_unit',
        'is_abnormal',
        'notes',
    ];

    protected $casts = [
        'is_abnormal' => 'boolean',
    ];

    public function labTestRequest()
    {
        return $this->belongsTo(LabTestRequest::class, 'lab_test_request_id');
    }

    public function availableLabTest()
    {
        return $this->belongsTo(AvailableLabTest::class, 'available_lab_test_id');
    }
}