<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\ResetPasswordOtp;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    public function showRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'Email tidak terdaftar di sistem',
        ]);

        $user = User::where('email', $request->email)->first();

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(10)
        ]);

        $user->notify(new ResetPasswordOtp($otp));

        session(['reset_email', $request->email]);

        return redirect()->route('password.otp')->with('success', 'Kode OTP telah dikirim ke email kamu.');
    }

    public function showForm()
    {
        if (!session('reset_email')) {
            return redirect()->route('password.request');
        }

        return view('auth.otp-verify');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $email = session('reset_email');
        $user = User::where('email', $email)->first();

        if (!$user || $user->otp_code !== $request->otp || now()->ifAfter($user->otp_expires_at)) {
            return back()->withErrors([
                'otp' => 'Kode OTP tidak valid atau sudah kadaluarsa',
            ]);
        }

        session(['reset_verified' => true]);

        return redirect()->route('password.reset.form');
    }

    public function resendOtp()
    {
        $email = session('reset_email');
        if (!$email) return redirect()->route('password.request');

        $user = User::where('email', $email)->first();
        if (!$user) return redirect()->route('password.request');

        // Cek cooldown — minimal 60 detik sejak OTP terakhir
        if (
            $user->otp_expires_at &&
            now()->diffInSeconds($user->otp_expires_at->subMinutes(9)) < 60
        ) {
            return back()->withErrors(['otp' => 'Tunggu 60 detik sebelum kirim ulang.']);
        }

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'otp_code'       => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        $user->notify(new ResetPasswordOtp($otp));

        return back()->with('success', 'Kode OTP baru telah dikirim.');
    }

    public function showResetForm()
    {
        if (!session('reset_email') || !session('reset_verified')) {
            return redirect()->route('password.request');
        }

        return view('auth.reset-password');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $email = session('reset_email');
        $user  = User::where('email', $email)->first();

        if (!$user) return redirect()->route('password.request');

        $user->update([
            'password'       => Hash::make($request->password),
            'otp_code'       => null,
            'otp_expires_at' => null,
        ]);

        session()->forget(['reset_email', 'reset_verified']);

        return redirect()->route('login')
            ->with('success', 'Password berhasil direset! Silakan masuk.');
    }
}
