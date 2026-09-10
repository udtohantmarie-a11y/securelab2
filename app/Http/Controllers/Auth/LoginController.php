<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;
use App\Mail\ResetPasswordMail;

class LoginController extends Controller
{
    // ==========================================
    // ACCOUNT REGISTRATION LOGIC
    // ==========================================

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        DB::table('users')->insert([
            'full_name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'Staff', // Default role required to prevent SQL null error
            'is_active' => 0,  // 0 means Pending/Inactive
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect('/')->with('success', 'Your account has been requested successfully and is currently pending admin approval.');
    }

    // ==========================================
    // STANDARD LOGIN & 2FA OTP LOGIC
    // ==========================================

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = DB::table('users')
            ->where('email', $request->email)
            ->first();

        if ($user && Hash::check($request->password, $user->password)) {

            // Check if account is not approved yet (is_active = 0)
            if ($user->is_active != 1) {
                return back()->withErrors([
                    'email' => 'Your account is currently inactive or pending admin approval.',
                ])->withInput($request->only('email'));
            }

            $otpCode = rand(100000, 999999);

            DB::table('users')->where('user_id', $user->user_id)->update([
                'verification_code' => $otpCode
            ]);

            session(['otp_pending_user_id' => $user->user_id]);

            Mail::to($user->email)->send(new OtpMail($otpCode, $user->full_name));

            return redirect()->route('otp.verify')->with('success', 'Authentication code sent! Please check your email inbox.');
        }

        return back()->withErrors([
            'email' => 'Invalid email or password.',
        ])->withInput($request->only('email'));
    }

    public function showOtpForm()
    {
        if (!session()->has('otp_pending_user_id')) {
            return redirect('/');
        }
        return view('auth.verify_otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric|digits:6'
        ]);

        $userId = session('otp_pending_user_id');
        if (!$userId) return redirect('/');

        $user = DB::table('users')->where('user_id', $userId)->first();

        if ($user && $user->verification_code == $request->otp) {

            DB::table('users')->where('user_id', $userId)->update([
                'verification_code' => null
            ]);

            // REMEMBER ME IS TRUE
            Auth::loginUsingId($userId, true);

            session()->forget('otp_pending_user_id');
            session()->regenerate();

            return redirect()->intended('dashboard')->with('success', 'Biometric & System Authorization Verified.');
        }

        return back()->withErrors(['otp' => 'Invalid or expired OTP code. Please try again.']);
    }

    // 🟢 UPDATED FUNCTION: MAGIC LINK / REDIRECT TO 2FA FORM
    public function autoVerifyOtp($code)
    {
        $user = DB::table('users')
            ->where('verification_code', $code)
            ->whereNotNull('verification_code')
            ->first();

        if ($user) {

            // 1. I-clear ang code para hindi na magamit ulit
            DB::table('users')->where('user_id', $user->user_id)->update([
                'verification_code' => null
            ]);

            // 2. I-login agad ang user (Remember Me = true)
            Auth::loginUsingId($user->user_id, true);

            // 3. Linisin ang old session
            session()->forget('otp_pending_user_id');
            session()->regenerate();

            // 4. DIRETTSO NA SA DASHBOARD (Wala nang 2FA page na bubukas)
            return redirect()->intended('dashboard')->with('success', 'Email automatically verified! Welcome back.');
        }

        return redirect('/')->withErrors(['email' => 'Invalid or expired magic link. Please log in again.']);
    }

    public function resendOtp()
    {
        $userId = session('otp_pending_user_id');
        if (!$userId) return redirect('/');

        $user = DB::table('users')->where('user_id', $userId)->first();
        $otpCode = rand(100000, 999999);

        DB::table('users')->where('user_id', $user->user_id)->update([
            'verification_code' => $otpCode
        ]);

        Mail::to($user->email)->send(new OtpMail($otpCode, $user->full_name));

        return back()->with('success', 'A new verification code has been sent to your email.');
    }

    // ==========================================
    // FORGOT PASSWORD LOGIC
    // ==========================================

    public function showForgotPasswordForm()
    {
        return view('auth.forgot_password');
    }

    public function sendResetOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = DB::table('users')->where('email', $request->email)->where('is_active', 1)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'No active account is registered with this email address.']);
        }

        $otpCode = rand(100000, 999999);

        DB::table('users')->where('user_id', $user->user_id)->update([
            'verification_code' => $otpCode
        ]);

        session(['reset_email' => $user->email]);

        Mail::to($user->email)->send(new ResetPasswordMail($otpCode, $user->full_name));

        return redirect()->route('password.reset.form')->with('success', 'Password reset code has been sent to your email.');
    }

    public function showResetPasswordForm()
    {
        if (!session()->has('reset_email')) {
            return redirect('/');
        }
        return view('auth.reset_password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric|digits:6',
            'password' => 'required|min:8|confirmed'
        ]);

        $email = session('reset_email');
        if (!$email) return redirect('/');

        $user = DB::table('users')->where('email', $email)->first();

        if ($user && $user->verification_code == $request->otp) {

            DB::table('users')->where('user_id', $user->user_id)->update([
                'password' => Hash::make($request->password), 
                'verification_code' => null
            ]);

            session()->forget('reset_email');

            session()->flash('success', 'Your password has been successfully reset! You can now log in.');
            return redirect('/');
        }

        return back()->withErrors(['otp' => 'Invalid or expired OTP code. Please try again.']);
    }

    // ==========================================
    // LOGOUT LOGIC
    // ==========================================

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    // ==========================================
    // CUSTOM PASSKEY LOGIC
    // ==========================================

    public function loginWithPasskey(Request $request)
    {
        $request->validate([
            'credential_id' => 'required|string'
        ]);

        $passkey = DB::table('passkeys')->where('credential_id', $request->credential_id)->first();

        if ($passkey) {
            $user = DB::table('users')->where('user_id', $passkey->user_id)->first();

            if ($user) {
                if ($user->is_active != 1) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Your account is currently inactive or pending admin approval.'
                    ], 401);
                }

                DB::table('passkeys')->where('id', $passkey->id)->update([
                    'last_used_at' => now()
                ]);

                // REMEMBER ME IS TRUE
                Auth::loginUsingId($user->user_id, true);
                session()->regenerate();

                return response()->json(['success' => true]);
            }
        }

        return response()->json([
            'success' => false, 
            'message' => 'Passkey not recognized or account deactivated.'
        ], 401);
    }
}