@extends('app')

@section('content')
<div class="pb-24 bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="fixed top-0 left-0 right-0 max-w-md mx-auto bg-white z-50 flex items-center px-4 py-3 border-b shadow-sm">
        <a href="/?tab=profile" class="mr-3">
             @svg('heroicon-o-arrow-left', 'w-6 h-6 text-gray-700')
        </a>
        <h1 class="font-bold text-lg text-gray-900">Riwayat Chat</h1>
    </div>

    <div class="mt-16 px-4 space-y-3">
        @forelse($chats as $chat)
            @php
                $lastMsg = \App\Models\ProductMessage::where('user_id', auth()->id())
                    ->where('product_id', $chat->product_id)
                    ->latest()
                    ->first();
                $unread = \App\Models\ProductMessage::where('user_id', auth()->id())
                    ->where('product_id', $chat->product_id)
                    ->where('reply', '!=', null)
                    ->where('replied_at', '>', $lastMsg->created_at) // Simple heuristic, better to have 'is_read_user'
                    ->count(); 
                    // Actually we don't track user read status for admin replies yet, so just show last message.
            @endphp
            <a href="{{ route('product.show', $chat->product_id) }}" class="block bg-white p-4 rounded-xl shadow-sm border border-gray-100 hover:bg-gray-50 transition-colors">
                <div class="flex items-start space-x-3">
                    <img src="{{ $chat->product->image ? Storage::url($chat->product->image) : 'https://via.placeholder.com/80.png?text=Product' }}" class="w-12 h-12 rounded-lg object-cover bg-gray-100">
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-start">
                            <h3 class="font-bold text-gray-900 text-sm truncate">{{ $chat->product->name }}</h3>
                            <span class="text-[10px] text-gray-500 whitespace-nowrap ml-2">{{ $lastMsg->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-xs text-gray-600 truncate mt-1">
                            @if($lastMsg->reply)
                                <span class="text-blue-600 font-bold">Admin:</span> {{ $lastMsg->reply }}
                            @else
                                <span class="text-gray-400">Anda:</span> {{ $lastMsg->message }}
                            @endif
                        </p>
                    </div>
                </div>
            </a>
        @empty
            <div class="flex flex-col items-center justify-center py-20 text-center">
                 @svg('heroicon-o-chat-bubble-left-right', 'w-16 h-16 text-gray-300 mb-4')
                 <h3 class="font-bold text-gray-900 mb-1">Belum Ada Chat</h3>
                 <p class="text-xs text-gray-500">Anda belum pernah mengirim pesan pada produk apapun.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
