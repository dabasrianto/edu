<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductMessageController extends Controller
{
    public function store(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'message' => 'required|string|max:1000',
        ]);

        \App\Models\ProductMessage::create([
            'user_id' => auth()->id(),
            'product_id' => $request->product_id,
            'message' => $request->message,
        ]);

        return response()->json(['message' => 'Pesan berhasil dikirim. Admin akan segera membalas.']);
    }
}
