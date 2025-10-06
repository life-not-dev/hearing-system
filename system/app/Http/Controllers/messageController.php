<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MessageModel;

class messageController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id'      => 'required|integer|exists:patients,id',
            'user_id'         => 'required|integer|exists:users,id',
            'sender_type'     => 'required|string',
            'receiver_id'     => 'required|integer',
            'receiver_type'   => 'required|string',
            'message_content' => 'required|string',
            'created_at'      => 'nullable|date',
            'read_at'         => 'nullable|date',
            'appointment_id'  => 'nullable|integer|exists:tbl_appointment,id',
            'branch_id'       => 'nullable|integer|exists:tbl_branch,branch_id',
        ]);
        $message = MessageModel::create($validated);
        return response()->json($message, 201);
    }

    public function conversation($user1, $user2)
    {
        $messages = MessageModel::where(function($q) use ($user1, $user2) {
            $q->where('user_id', $user1)->where('receiver_id', $user2);
        })->orWhere(function($q) use ($user1, $user2) {
            $q->where('user_id', $user2)->where('receiver_id', $user1);
        })->orderBy('created_at')->get();

        return response()->json($messages);
    }
}
