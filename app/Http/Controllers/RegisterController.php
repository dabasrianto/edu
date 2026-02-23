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

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'whatsapp' => $request->whatsapp,
            'address' => $request->address,
            'role' => 'user',
            'is_active' => false, // Default pending
        ]);

        return back()->with('success_register', 'Registrasi berhasil! Akun Anda sedang diverifikasi oleh Admin. Mohon tunggu.');
    }
}
