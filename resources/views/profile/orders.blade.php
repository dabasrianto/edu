@extends('app')

@section('content')
<div class="pb-24 bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="fixed top-0 left-0 right-0 max-w-md mx-auto bg-white z-50 flex items-center px-4 py-3 border-b shadow-sm">
        <a href="/?tab=profile" class="mr-3">
             @svg('heroicon-o-arrow-left', 'w-6 h-6 text-gray-700')
        </a>
        <h1 class="font-bold text-lg text-gray-900">Orderan Saya</h1>
    </div>

    <div class="mt-16 px-4 space-y-4">
        @forelse($orders as $order)
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                <div class="flex justify-between items-center mb-3 pb-3 border-b border-gray-50">
                    <div>
                        <span class="text-[10px] text-gray-400">Order ID</span>
                        <p class="text-xs font-bold text-gray-900">#{{ $order->id }}</p>
                    </div>
                    @php
                        $statusColors = [
                            'pending' => 'bg-orange-100 text-orange-700',
                            'processing' => 'bg-blue-100 text-blue-700',
                            'shipped' => 'bg-purple-100 text-purple-700',
                            'completed' => 'bg-green-100 text-green-700',
                            'cancelled' => 'bg-red-100 text-red-700',
                        ];
                    @endphp
                    <span class="px-2 py-1 rounded text-[10px] font-bold {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-700' }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>

                <div class="space-y-2 mb-3">
                    @foreach($order->items as $item)
                        <div class="flex items-center space-x-3">
                             <img src="{{ $item->product->image ? Storage::url($item->product->image) : 'https://via.placeholder.com/60.png' }}" class="w-10 h-10 rounded object-cover bg-gray-100">
                             <div class="flex-1 min-w-0">
                                 <p class="text-xs font-medium text-gray-900 truncate">{{ $item->product->name }}</p>
                                 <p class="text-[10px] text-gray-500">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                             </div>
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-between items-center pt-2 border-t border-gray-50">
                    <span class="text-xs text-gray-500">Total Belanja</span>
                    <span class="text-sm font-bold text-orange-600">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
                
                @if($order->status == 'pending' && !$order->payment_proof)
                    <div class="mt-3">
                         <button onclick='openOrderPaymentModal(@json($order))' class="w-full bg-blue-900 text-white text-xs py-2 rounded-lg font-bold hover:bg-blue-800 transition-colors">
                            Bayar Sekarang
                         </button>
                    </div>
                @elseif($order->status == 'pending' && $order->payment_proof)
                     <div class="mt-3 bg-yellow-50 text-yellow-700 p-2 rounded text-xs text-center border border-yellow-200">
                        Menunggu Verifikasi Admin
                    </div>
                @endif
            </div>
        @empty
             <div class="flex flex-col items-center justify-center py-20 text-center">
                 @svg('heroicon-o-shopping-bag', 'w-16 h-16 text-gray-300 mb-4')
                 <h3 class="font-bold text-gray-900 mb-1">Belum Ada Order</h3>
                 <p class="text-xs text-gray-500">Anda belum pernah melakukan pemesanan produk.</p>
                 <a href="/?tab=admin" class="mt-4 px-4 py-2 bg-blue-900 text-white rounded-lg text-xs font-bold">Belanja Sekarang</a>
            </div>
        @endforelse
    </div>
</div>

@include('partials.modals.order-payment')
@endsection
