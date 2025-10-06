<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;

    protected $table = 'tbl_test';
    protected $primaryKey = 'test_id';
    protected $fillable = [
        'appointment_id',
        'patient_id',
        'hearing_aid_id',
        'test_type',
        'test_note',
        'test_result',
        'test_payload',
        'test_date',
    ];
}
