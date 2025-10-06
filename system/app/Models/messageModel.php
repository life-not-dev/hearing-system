<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class messageModel extends Model
{
        protected $table = 'tbl_message';
        protected $primaryKey = 'message_id';

        protected $fillable = [
            'patient_id',
            'user_id',
            'sender_type',
            'receiver_id',
            'receiver_type',
            'message_content',
            'created_at',
            'read_at',
            'appointment_id',
            'branch_id',
        ];
}
