<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientRecord extends Model
{
    use HasFactory;

    protected $table = 'tbl_patient_record';
    protected $primaryKey = 'patient_record_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'patient_id',
        'billing_id',
        'patient_record_date_registered',
    ];

    protected $casts = [
        'patient_record_date_registered' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }
}
