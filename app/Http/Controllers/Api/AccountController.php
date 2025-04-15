<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        
        $user = Auth::user();
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Current password is incorrect',
                'errors' => [
                    'current_password' => ['The provided password does not match our records.']
                ]
            ], 422);
        }
        
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Password updated successfully',
        ]);
    }
}