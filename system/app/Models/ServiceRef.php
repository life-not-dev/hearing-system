<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRef extends Model
{
    use HasFactory;

    protected $table = 'tbl_services';
    protected $primaryKey = 'service_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'service_name',
        'service_price',
        'service_status',
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'service_id', 'service_id');
    }
}
