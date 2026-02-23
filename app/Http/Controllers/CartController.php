<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CartController extends Controller
{
    public function store(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $item = \App\Models\CartItem::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($item) {
            $item->increment('quantity', $request->quantity ?? 1);
        } else {
            \App\Models\CartItem::create([
                'user_id' => auth()->id(),
                'product_id' => $request->product_id,
                'quantity' => $request->quantity ?? 1,
            ]);
        }

        return response()->json([
            'message' => 'Produk berhasil ditambahkan ke keranjang.',
            'count' => \App\Models\CartItem::where('user_id', auth()->id())->count()
        ]);
    }

    public function count()
    {
        $count = auth()->check() 
            ? \App\Models\CartItem::where('user_id', auth()->id())->count() 
            : 0;

        return response()->json(['count' => $count]);
    }

    public function index()
    {
        $items = \App\Models\CartItem::where('user_id', auth()->id())
            ->with('product')
            ->get();

        return response()->json(['items' => $items]);
    }

    public function destroy(Request $request)
    {
        $request->validate(['id' => 'required']);

        \App\Models\CartItem::where('user_id', auth()->id())
            ->where('id', $request->id)
            ->delete();

        return response()->json(['message' => 'Item deleted']);
    }
}
