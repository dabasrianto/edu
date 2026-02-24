<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function registerUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'whatsapp' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->with('error_register', 'Registrasi gagal. Cek inputan Anda.')->withErrors($validator)->withInput();
        }

        $appSettings = \App\Models\AppSetting::firstOrCreate(['key' => 'main_settings']);
        $otpEnabled = $appSettings->otp_config['otp_register'] ?? false;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'whatsapp' => $request->whatsapp,
            'address' => $request->address,
            'role' => 'user',
            'is_active' => $otpEnabled ? false : true, // If OTP enabled, keep inactive until verified
        ]);

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
                
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'requires_otp' => true,
                        'email' => $user->email,
                        'message' => 'Registrasi berhasil! Silakan cek email Anda untuk kode OTP.'
                    ]);
                }

                // If not ajax, we might need a separate OTP page, but for now we assume ajax from the unified auth tab
                return back()->with('success_register', 'Registrasi berhasil! Silakan cek email Anda untuk kode OTP.');
            } catch (\Exception $e) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Registrasi berhasil, tetapi gagal mengirim email OTP: ' . $e->getMessage()
                    ], 500);
                }
                return back()->with('error_register', 'Gagal mengirim email OTP.');
            }
        }

        if ($request->ajax()) {
             return response()->json([
                'success' => true,
                'redirect' => '/?tab=profil',
                'message' => 'Registrasi berhasil! Selamat datang.'
            ]);
        }

        return back()->with('success_register', 'Registrasi berhasil! Selamat datang.');
    }
}
