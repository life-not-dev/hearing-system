<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:users,name,' . Auth::id(),
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'username.required' => 'Username is required.',
            'username.unique' => 'This username is already taken.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $user = Auth::user();
            
            // Update username
            $user->name = $request->username;
            
            // Update password if provided
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            
            $user->save();

            return back()->with('success', 'Profile updated successfully!');
            
        } catch (\Exception $e) {
            return back()
                ->with('error', 'An error occurred while updating your profile. Please try again.')
                ->withInput();
        }
    }
}
