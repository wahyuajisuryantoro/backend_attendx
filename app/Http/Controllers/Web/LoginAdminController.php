<?php

namespace App\Http\Controllers\Web;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginAdminController extends Controller
{
    public function index()
    {
        if (Auth::check() && Auth::user()->is_admin) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);


        $user = User::where('username', $request->username)
            ->where('is_admin', true)
            ->where('is_active', true)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'username' => 'Invalid Username or Password'
            ])->withInput();
        }

        Auth::login($user);

        $user->createToken('auth_token')->plainTextToken;

        return redirect()->route('dashboard')
            ->with('success', 'Success Login');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            Auth::user()->tokens()->delete();
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login')
            ->with('success', 'You has been logout');
    }
}