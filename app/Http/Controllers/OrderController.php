<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function checkout(Request $request)
    {
        $request->validate([
            'address' => 'required|string',
            'items' => 'required|array',
            'items.*.id' => 'required|exists:cart_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.note' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $user = auth()->user();
            $cartItems = CartItem::where('user_id', $user->id)
                ->whereIn('id', collect($request->items)->pluck('id'))
                ->with('product')
                ->get();

            if ($cartItems->isEmpty()) {
                return response()->json(['message' => 'Keranjang kosong atau item tidak ditemukan.'], 400);
            }

            $totalAmount = 0;
            $orderItemsData = [];

            foreach ($cartItems as $item) {
                // Find matching request item to get updated quantity/note
                $reqItem = collect($request->items)->firstWhere('id', $item->id);
                $quantity = $reqItem['quantity'];
                $note = $reqItem['note'] ?? null;
                $price = $item->product->price * $quantity;

                $totalAmount += $price;

                $orderItemsData[] = [
                    'product' => $item->product,
                    'quantity' => $quantity,
                    'price' => $item->product->price,
                    'note' => $note,
                ];
            }

            // 1. Create Order
            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => $totalAmount,
                'status' => 'pending', 
                'shipping_address' => $request->address,
                'admin_note' => null,
            ]);

            // 2. Create Order Items
            foreach ($orderItemsData as $data) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $data['product']->id,
                    'quantity' => $data['quantity'],
                    'price' => $data['price'], // Price per unit
                    'note' => $data['note'],
                ]);
            }

            // 3. Clear Cart (Only checked out items)
            CartItem::where('user_id', $user->id)
                ->whereIn('id', $cartItems->pluck('id'))
                ->delete();

            DB::commit();

            return response()->json([
                'message' => 'Pesanan berhasil dibuat.',
                'order_id' => $order->id,
                'redirect_url' => route('profile.orders') 
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }

    public function uploadPayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'payment_proof' => 'required|image|max:2048'
        ]);

        $user = auth()->user();
        $order = Order::where('id', $request->order_id)->where('user_id', $user->id)->firstOrFail();

        $path = $request->file('payment_proof')->store('order_payments', 'public');

        $order->update([
            'payment_proof' => $path,
            'status' => 'pending' // Tetap pending menunggu verifikasi admin
        ]);

        return response()->json(['message' => 'Bukti pembayaran berhasil diupload']);
    }

    public function payWithBalance(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id'
        ]);

        $user = auth()->user();
        $order = Order::where('id', $request->order_id)->where('user_id', $user->id)->firstOrFail();

        if ($order->status !== 'pending') {
            return response()->json(['message' => 'Order ini tidak dalam status pending.'], 400);
        }

        // 1. Check Balance
        if ($user->balance < $order->total_amount) {
            return response()->json(['message' => 'Saldo tidak mencukupi'], 400);
        }

        try {
            DB::beginTransaction();

            // 2. Deduct Balance
            $user->decrement('balance', $order->total_amount);

            // 3. Update Order Status
            $order->update([
                'status' => 'processing', // Langsung diproses krn saldo otomatis
                'payment_method' => 'balance' 
            ]);

            DB::commit();
            return response()->json(['message' => 'Pembayaran berhasil. Pesanan sedang diproses.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal memproses pembayaran: ' . $e->getMessage()], 500);
        }
    }
}
