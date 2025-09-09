<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OtpCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log; 

class OtpController extends Controller
{
    /**
     * Send OTP for login
     */
public function sendLogin(Request $request)
{
    $request->validate([
        'phone' => ['required', 'string', 'regex:/^\+?[0-9]{8,15}$/'],
    ]);

    $user = User::where('phone', $request->phone)->first();
    if (! $user) {
        return response()->json(['error' => 'Phone not registered. Please sign up.'], 422);
    }

    $code = rand(100000, 999999);

    OtpCode::create([
        'channel'    => 'sms',
        'recipient'  => $request->phone,
        'code'       => $code,
        'expires_at' => now()->addMinutes(5),
    ]);

    // ✅ Log the OTP into storage/logs/laravel.log
    Log::info("Login OTP {$code} sent to {$request->phone}");

    // ✅ Return OTP in response (only for local/dev testing)
    return response()->json([
        'message' => 'OTP sent!',
        'otp' => $code   // remove in production
    ]);
}


    /**
     * Verify OTP for login
     */
    public function verifyLogin(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'string', 'regex:/^\+?[0-9]{8,15}$/'],
            'code'  => ['required', 'digits:6'],
        ]);

        $otp = OtpCode::where('recipient', $request->phone)
            ->where('code', $request->code)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (! $otp) {
            return back()->withErrors(['code' => 'Invalid or expired OTP']);
        }

        $user = User::where('phone', $request->phone)->first();
        if (! $user) {
            return back()->withErrors(['phone' => 'User not found.']);
        }

        $otp->update(['used_at' => now()]);
        auth()->login($user);

        return redirect()->route('dashboard');
    }

    /**
     * Verify OTP for registration and create user
     */
    public function verify(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'phone'    => 'required|string|regex:/^\+?[0-9]{8,15}$/',
            'code'     => 'required|digits:6',
            'password' => 'required|string|min:8',
            'email'    => 'nullable|email|unique:users,email',
        ]);

        $otp = OtpCode::where('recipient', $request->phone)
            ->where('code', $request->code)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (! $otp) {
            return back()->withErrors(['code' => 'Invalid or expired OTP']);
        }

        $user = User::create([
            'name'     => $request->name,
            'phone'    => $request->phone,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'username' => strtolower(str_replace(' ', '_', $request->name) . "_buzz_" . rand(1000,9999)),
        ]);

        $otp->update(['used_at' => now()]);

        if ($user->email) {
            $user->sendEmailVerificationNotification();
            auth()->login($user);
            return redirect()->route('verification.notice');
        }

        auth()->login($user);
        return redirect()->route('dashboard');
    }

    public function sendRegister(Request $request)
{
    $request->validate([
        'phone' => ['required', 'string', 'regex:/^\+?[0-9]{8,15}$/'],
    ]);

    // Make sure this phone is NOT already registered
    if (User::where('phone', $request->phone)->exists()) {
        return response()->json(['error' => 'Phone already registered. Please log in.'], 422);
    }

    $code = rand(100000, 999999);

    OtpCode::create([
        'channel'    => 'sms',
        'recipient'  => $request->phone,
        'code'       => $code,
        'expires_at' => now()->addMinutes(5),
    ]);

    logger()->info("Registration OTP {$code} sent to {$request->phone}");

    return response()->json(['message' => 'Registration OTP sent!']);
}

}
