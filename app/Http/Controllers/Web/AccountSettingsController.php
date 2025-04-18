<?php

namespace App\Http\Controllers\Web;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Jenssegers\Agent\Agent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AccountSettingsController extends Controller
{
    public function index(){
        $title = "Profile Settings";
        $user = Auth::user();
        $userProfile = $user->profile;
        
        $agent = new Agent();
        $deviceInfo = [
            'device' => $agent->device(),
            'platform' => $agent->platform(),
            'browser' => $agent->browser(),
            'is_desktop' => $agent->isDesktop(),
            'is_mobile' => $agent->isMobile(),
            'is_tablet' => $agent->isTablet(),
            'ip_address' => request()->ip(),
            'last_activity' => now()->format('M d \a\t h:i A')
        ];

        Session::put('current_device', $deviceInfo);

        return view("account_settings.account_settings_index", compact('title','user', 'userProfile', 'deviceInfo',));
    }

    public function updatePassword(Request $request)
    {

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
            'admin_key' => 'required'
        ]);

        if ($request->admin_key !== config('app.admin_creation_key')) {
            return redirect()->back()->with('error', 'Invalid admin key. Password update failed.');
        }
        
        $user = Auth::user();
        
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->back()->with('success', 'Password updated successfully.');
    }
    
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users_profile', 'email')->ignore(Auth::user()->profile->id)],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'admin_key' => 'required'
        ]);
        
        if ($request->admin_key !== config('app.admin_creation_key')) {
            return redirect()->back()->with('error', 'Invalid admin key. Profile update failed.');
        }
        
        $user = Auth::user();
        $profile = $user->profile;
        
        $profile->name = $request->name;
        $profile->email = $request->email;
        $profile->phone = $request->phone;
        $profile->address = $request->address;   
        $profile->save();

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    public function verifyAdminKey(Request $request)
    {
        $request->validate([
            'admin_key' => 'required'
        ]);
        
        $isValid = $request->admin_key === config('app.admin_creation_key');
        
        return response()->json([
            'status' => $isValid,
            'message' => $isValid ? 'Valid admin key' : 'Invalid admin key'
        ]);
    }
}
