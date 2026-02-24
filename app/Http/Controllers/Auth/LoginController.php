<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::validate($credentials)) {
            $user = User::where('email', $request->email)->first();
            
            if (!$user->is_active && $user->role !== 'admin') {
                 return back()->withErrors([
                    'email' => 'Akun Anda belum aktif. Silakan hubungi admin.',
                ])->onlyInput('email');
            }

            $appSettings = \App\Models\AppSetting::firstOrCreate(['key' => 'main_settings']);
            $otpEnabled = $appSettings->otp_config['otp_login'] ?? false;

            if ($otpEnabled) {
                // Generate OTP
                $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
                $user->otp_code = $otp;
                $user->otp_expires_at = \Carbon\Carbon::now()->addMinutes(10);
                $user->save();

                // Send Email
                try {
                    \App\Http\Controllers\EmailSettingController::applyDatabaseMailConfig();
                    \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OTPMail($otp));
                    
                    return response()->json([
                        'success' => true,
                        'requires_otp' => true,
                        'email' => $user->email,
                        'message' => 'Kode OTP telah dikirim ke email Anda.'
                    ]);
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal mengirim email OTP: ' . $e->getMessage()
                    ], 500);
                }
            }

            if (Auth::attempt($credentials, $request->boolean('remember'))) {
                $request->session()->regenerate();

                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'redirect' => '/?tab=profil'
                    ]);
                }

                return redirect('/?tab=profil');
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah.'
            ], 401);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
 
        $request->session()->invalidate();
 
        $request->session()->regenerateToken();
 
        return redirect('/');


    }
}
