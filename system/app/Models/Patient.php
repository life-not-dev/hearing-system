<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $table = 'tbl_patient';
    protected $primaryKey = 'patient_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'patient_firstname',
        'patient_surname',
        'patient_middlename',
        'patient_birthdate',
        'patient_age',
        'patient_gender',
        'patient_email',
        'patient_contact_number',
        'patient_address',
        'patient_referred_by',
        'patient_medical_history',
    ];

    protected $casts = [
        'patient_birthdate' => 'date',
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'patient_id', 'patient_id');
    }

    public function records()
    {
        return $this->hasMany(PatientRecord::class, 'patient_id', 'patient_id');
    }
}
