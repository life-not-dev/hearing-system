<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HearingAid extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand', 'model', 'price'
    ];
}
