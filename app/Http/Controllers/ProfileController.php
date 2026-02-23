<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'nip' => ['nullable', 'string', 'max:50'],
            'avatar' => ['nullable', 'image', 'max:2048'], // Max 2MB
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'whatsapp' => $request->whatsapp,
            'address' => $request->address,
            'nip' => $request->nip,
        ];

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;
        }

        $user->update($data);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        auth()->user()->update([
            'password' => $request->password,
        ]);

        return back()->with('success', 'Password berhasil diubah!');
    }
    public function chats()
    {
        $chats = \App\Models\ProductMessage::where('user_id', auth()->id())
            ->with('product')
            ->select('product_id', \DB::raw('max(created_at) as last_message_time'))
            ->groupBy('product_id')
            ->orderBy('last_message_time', 'desc')
            ->get();

        return view('profile.chats', compact('chats'));
    }

    public function orders()
    {
        $orders = \App\Models\Order::where('user_id', auth()->id())
            ->with(['items.product'])
            ->latest()
            ->get();

        return view('profile.orders', compact('orders'));
    }
}
