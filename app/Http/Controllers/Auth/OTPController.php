<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OTPController extends Controller
{
    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
            'action' => 'required|in:login,register',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User tidak ditemukan.'], 404);
        }

        if ($user->otp_code !== $request->otp) {
            return response()->json(['success' => false, 'message' => 'Kode OTP salah.'], 400);
        }

        if (Carbon::now()->gt($user->otp_expires_at)) {
            return response()->json(['success' => false, 'message' => 'Kode OTP telah kedaluwarsa.'], 400);
        }

        // Clear OTP
        $user->otp_code = null;
        $user->otp_expires_at = null;
        
        if ($request->action === 'register') {
            $user->is_active = true;
            $user->email_verified_at = now();
        }
        
        $user->save();

        // Login the user
        Auth::login($user);

        return response()->json([
            'success' => true,
            'message' => 'Verifikasi berhasil.',
            'redirect' => '/?tab=profil'
        ]);
    }

    public function resend(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User tidak ditemukan.'], 404);
        }

        // Generate new OTP
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->otp_code = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(10);
        $user->save();

        // Send Email
        try {
            \App\Http\Controllers\EmailSettingController::applyDatabaseMailConfig();
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OTPMail($otp));
            return response()->json(['success' => true, 'message' => 'Kode OTP baru telah dikirim ke email Anda.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengirim email: ' . $e->getMessage()], 500);
        }
    }
}
