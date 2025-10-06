<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $table = 'tbl_appointment';

    protected $fillable = [
        'appointment_time', 'appointment_date', 'seen_at',
        'status', 'confirmed_at', 'canceled_at',
        'patient_type', // kept for now; can be moved to patient later
        'purpose',
        // FKs are set via forceFill after create or guarded by migration existence
        'patient_id', 'branch_id', 'service_id',
    ];

    protected $casts = [
        'appointment_time' => 'datetime:H:i:s',
        'appointment_date' => 'date',
        'seen_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'canceled_at' => 'datetime',
        // only appointment-level date/times here
    ];

    // Relations to normalized tables (nullable FKs)
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }

    public function branchRef()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'branch_id');
    }

    public function serviceRef()
    {
        return $this->belongsTo(ServiceRef::class, 'service_id', 'service_id');
    }

    public function tests()
    {
        return $this->hasMany(Test::class, 'appointment_id', 'id');
    }
}
