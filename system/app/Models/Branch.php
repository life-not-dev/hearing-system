<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $table = 'tbl_branch';
    protected $primaryKey = 'branch_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'branch_name',
        'branch_address',
        'branch_operating_hours',
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'branch_id', 'branch_id');
    }
}
