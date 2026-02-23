@extends('app')

@section('content')
<div class="pb-24 bg-white min-h-screen">
    <!-- Header -->
    <div class="fixed top-0 left-0 right-0 max-w-md mx-auto bg-white z-50 flex items-center justify-between px-4 py-3 border-b shadow-sm">
        <div class="flex items-center flex-1 min-w-0 mr-4">
            <a href="/" class="mr-3 flex-shrink-0">
                @svg('heroicon-o-arrow-left', 'w-6 h-6 text-gray-700')
            </a>
            <h1 class="font-bold text-lg text-gray-900 truncate">Detail Produk</h1>
        </div>
        
        <!-- Cart Icon in Header -->
        <div class="relative flex-shrink-0">
             <a href="#" class="text-gray-600 relative block">
                @svg('heroicon-o-shopping-bag', 'w-6 h-6')
                <span class="cart-badge absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold px-1 rounded-full hidden">0</span>
            </a>
        </div>
    </div>

    <!-- Product Image -->
    <div class="mt-14 w-full aspect-square bg-gray-100">
        <img src="{{ $product->image ? Storage::url($product->image) : 'https://placehold.co/600x600?text=' . urlencode($product->name) }}" class="w-full h-full object-cover">
    </div>

    <!-- Product Info -->
    <div class="p-4 space-y-3">
        <div class="flex justify-between items-start">
            <h2 class="text-xl font-bold text-gray-900 leading-tight">{{ $product->name }}</h2>
        </div>

        <div class="flex items-center space-x-2">
            <span class="text-2xl font-bold text-orange-600">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
        </div>

        <div class="flex items-center space-x-4 text-sm text-gray-500">
            <div class="flex items-center">
                @svg('heroicon-s-star', 'w-4 h-4 text-yellow-400 mr-1')
                <span class="font-bold text-gray-800">{{ $product->rating }}</span>
            </div>
            <div class="w-px h-4 bg-gray-300"></div>
            <div>
                Terjual <span class="font-bold text-gray-800">{{ $product->sold_count }}</span>
            </div>
        </div>

        <hr class="border-gray-100 my-2">

        <div>
            <h3 class="font-bold text-gray-900 mb-2">Deskripsi Produk</h3>
            <p class="text-gray-600 text-sm leading-relaxed whitespace-pre-line">
                {{ $product->description ?? 'Tidak ada deskripsi.' }}
            </p>
        </div>
    </div>
</div>

<!-- Bottom Sticky Action Bar -->
<div class="fixed bottom-0 left-0 right-0 max-w-md mx-auto bg-white border-t p-4 z-50 flex items-center space-x-3 shadow-[0_-2px_10px_rgba(0,0,0,0.05)]">
    <button onclick="document.getElementById('chat-modal').classList.remove('hidden')" class="border border-green-600 text-green-600 p-3 rounded-xl font-bold flex items-center justify-center hover:bg-green-50 transition-colors">
        @svg('heroicon-o-chat-bubble-left-ellipsis', 'w-6 h-6')
    </button>
    <button id="add-to-cart-btn" class="flex-1 bg-blue-900 text-white font-bold py-3 rounded-xl shadow-lg hover:bg-blue-800 transition-colors flex items-center justify-center space-x-2">
        @svg('heroicon-o-shopping-cart', 'w-5 h-5')
        <span>+ Keranjang</span>
    </button>
</div>

<!-- Chat Modal -->
<div id="chat-modal" class="fixed inset-0 z-[60] flex items-center justify-center hidden" style="background-color: rgba(0, 0, 0, 0.5);">
    <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-sm mx-4 relative animate-fade-in-up flex flex-col max-h-[80vh]">
        <h3 class="text-lg font-bold text-gray-900 mb-4 flex-shrink-0">Chat dengan Admin</h3>
        
        <!-- Chat History -->
        <div id="chat-history" class="flex-1 overflow-y-auto mb-4 space-y-3 p-2 border rounded-lg bg-gray-50 text-sm">
            <!-- Messages will be loaded here via JS or Blade -->
            @auth
                @foreach(\App\Models\ProductMessage::where('user_id', auth()->id())->where('product_id', $product->id)->orderBy('created_at', 'asc')->get() as $msg)
                    <div class="flex flex-col space-y-1 {{ $msg->reply ? 'items-start' : 'items-end' }}">
                        <!-- User Message -->
                        <div class="bg-blue-100 text-blue-900 px-3 py-2 rounded-lg rounded-tr-none max-w-[85%] self-end">
                            {{ $msg->message }}
                        </div>
                        <span class="text-[10px] text-gray-400 self-end">{{ $msg->created_at->format('H:i') }}</span>

                        <!-- Admin Reply -->
                        @if($msg->reply)
                            <div class="bg-gray-200 text-gray-800 px-3 py-2 rounded-lg rounded-tl-none max-w-[85%] self-start">
                                {{ $msg->reply }}
                            </div>
                            <span class="text-[10px] text-gray-400 self-start">Admin • {{ $msg->replied_at ? \Carbon\Carbon::parse($msg->replied_at)->format('H:i') : '' }}</span>
                        @endif
                    </div>
                @endforeach
                @if(\App\Models\ProductMessage::where('user_id', auth()->id())->where('product_id', $product->id)->count() == 0)
                    <p class="text-center text-gray-400 italic py-4">Belum ada pesan. Tanyakan sesuatu!</p>
                @endif
            @else
                <p class="text-center text-gray-400">Silakan login untuk melihat riwayat chat.</p>
            @endauth
        </div>

        <textarea id="chat-message" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 mb-4 flex-shrink-0" rows="3" placeholder="Tulis pesan..."></textarea>
        <div class="flex space-x-2 flex-shrink-0">
             <button onclick="document.getElementById('chat-modal').classList.add('hidden')" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Ttutup</button>
             <button id="send-chat-btn" class="flex-1 px-4 py-2 bg-blue-900 text-white rounded-lg hover:bg-blue-800">Kirim</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        window.updateCartBadge(); 

        // Add to Cart Logic
        const btn = document.getElementById('add-to-cart-btn');
        btn.addEventListener('click', () => {
             // ... existing cart logic ...
             btn.disabled = true;
             btn.classList.add('opacity-75', 'cursor-not-allowed');
             fetch('{{ route("cart.store") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ product_id: {{ $product->id }}, quantity: 1 })
            })
            .then(res => res.json())
            .then(data => {
                document.querySelectorAll('.cart-badge').forEach(el => {
                    el.innerText = data.count;
                    if(data.count > 0) el.classList.remove('hidden');
                });
                alert(data.message);
            })
            .catch(err => alert('Gagal menambahkan ke keranjang.'))
            .finally(() => {
                btn.disabled = false;
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
            });
        });

        // Chat Logic
        const sendBtn = document.getElementById('send-chat-btn');
        sendBtn.addEventListener('click', () => {
            const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
            
            if (!isLoggedIn) {
                if(confirm('Silakan login terlebih dahulu untuk mengirim pesan. Ingin login sekarang?')) {
                    window.location.href = '{{ route("login") }}';
                }
                return;
            }

            const msg = document.getElementById('chat-message').value;
            if(!msg.trim()) { alert('Mohon isi pesan.'); return; }

            sendBtn.disabled = true;
            sendBtn.innerText = 'Mengirim...';

            fetch('{{ route("product.message") }}', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json', 
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ product_id: {{ $product->id }}, message: msg })
            })
            .then(async res => {
                if (res.status === 401) {
                     alert('Sesi Anda telah berakhir. Silakan login kembali.');
                     window.location.href = '{{ route("login") }}';
                     throw new Error('Unauthorized');
                }
                
                const data = await res.json().catch(() => ({}));
                
                if (!res.ok) {
                    throw new Error(data.message || 'Gagal mengirim pesan.');
                }
                return data;
            })
            .then(data => {
                alert(data.message);
                document.getElementById('chat-modal').classList.add('hidden');
                document.getElementById('chat-message').value = '';
            })
            .catch(err => {
                if (err.message !== 'Unauthorized') {
                    console.error(err);
                    alert(err.message || 'Gagal mengirim pesan.');
                }
            })
            .finally(() => {
                sendBtn.disabled = false;
                sendBtn.innerText = 'Kirim';
            });
        });
    });
</script>
@endsection
