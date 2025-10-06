<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Billing extends Model
{
    use HasFactory;

    protected $table = 'tbl_billing';
    protected $primaryKey = 'billing_id';

    protected $fillable = [
        'test_id',
        'patient_id',
        'billing_date',
        'billing_original_bill',
        'billing_discount_bill',
        'billing_total_bill',
        'billing_patient_type',
    ];

    public function test()
    {
        return $this->belongsTo(Test::class, 'test_id', 'test_id');
    }
}
