<div id="view-cart" class="tab-content hidden fade-in py-6 pb-32">
    <!-- Header Cart -->
    <div class="px-4 mb-6 sticky top-0 bg-white z-10 py-2 border-b border-gray-100 flex items-center space-x-3">
        <button onclick="switchTab('home')" class="p-2 -ml-2 rounded-full hover:bg-gray-100 text-gray-600">
            @svg('heroicon-s-arrow-left', 'w-5 h-5')
        </button>
        <h2 class="text-xl font-bold text-gray-900">Detail Orderan</h2>
    </div>

    <!-- Cart Items Container -->
    <div class="px-4 space-y-4" id="cart-items-container">
        <!-- Javascript will populate this -->
        <div class="text-center py-10 bg-gray-50 rounded-xl border border-dashed border-gray-300">
            @svg('heroicon-o-shopping-bag', 'w-12 h-12 mx-auto mb-2 text-gray-300')
            <p class="text-gray-400 text-sm">Keranjang kosong.</p>
            <button onclick="switchTab('home')" class="mt-4 text-blue-600 font-bold text-xs">Mulai Belanja</button>
        </div>
    </div>

    <!-- Checkout Section -->
    <div id="checkout-section" class="hidden px-4 mt-8 border-t border-gray-100 pt-6">
        <h3 class="font-bold text-lg mb-4">Pengiriman</h3>
        
        <div class="mb-4">
            <label class="text-xs font-bold text-gray-700 block mb-2">Alamat Lengkap <span class="text-red-500">*</span></label>
            <textarea id="checkout-address" rows="3" class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:border-blue-500 outline-none" placeholder="Masukkan alamat lengkap pengiriman... (Jalan, No Rumah, RT/RW, Kelurahan, Kecamatan)"></textarea>
            @if(auth()->check() && auth()->user()->address)
                <div class="mt-1 text-right">
                    <button onclick="document.getElementById('checkout-address').value = '{{ auth()->user()->address }}'" class="text-[10px] text-blue-600 font-bold hover:underline">
                        Gunakan Alamat dari Profil
                    </button>
                </div>
            @endif
        </div>

        <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 mb-6">
            <div class="flex justify-between items-center mb-2">
                <span class="text-gray-600 text-sm">Total Item:</span>
                <span class="font-bold text-gray-900" id="checkout-total-items">0</span>
            </div>
             <div class="flex justify-between items-center">
                <span class="text-gray-600 text-sm">Total Harga:</span>
                <span class="font-bold text-xl text-orange-600" id="checkout-total-price">Rp 0</span>
            </div>
        </div>

        <button onclick="processCheckout()" id="btn-checkout" class="w-full bg-blue-900 text-white font-bold py-3.5 rounded-xl hover:bg-blue-800 shadow-lg shadow-blue-900/10 active:scale-[0.98] transform transition-transform flex items-center justify-center">
             Checkout
        </button>
    </div>
</div>

<script>
    let cartItemsData = [];

    // Function called when switching to 'cart' tab
    async function loadCart() {
         const container = document.getElementById('cart-items-container');
         const checkoutSec = document.getElementById('checkout-section');
         
         if(!isAuthenticated) {
             container.innerHTML = `
                <div class="text-center py-10">
                    <p class="text-gray-500 text-sm mb-4">Silakan login untuk melihat keranjang.</p>
                    <button onclick="switchTab('login')" class="bg-blue-900 text-white px-6 py-2 rounded-lg font-bold text-xs">Login Sekarang</button>
                </div>
             `;
             if(checkoutSec) checkoutSec.classList.add('hidden');
             return;
         }

         container.innerHTML = '<div class="text-center py-4 text-gray-400 text-sm">Memuat keranjang...</div>';

         try {
             // We need a route to GET cart items.
             // Currently we only have /cart/count. Let's assume we'll add /cart/items or just use logic here.
             // Wait, I haven't created GET /cart/items route yet. I should add it to web.php.
             // For now let's assume the endpoint is /cart/data
             const response = await fetch('/cart/data');
             if(!response.ok) throw new Error('Failed to load');
             
             const data = await response.json();
             cartItemsData = data.items; // Store globally

             if(cartItemsData.length === 0) {
                 container.innerHTML = `
                    <div class="text-center py-10 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                        @svg('heroicon-o-shopping-bag', 'w-12 h-12 mx-auto mb-2 text-gray-300')
                        <p class="text-gray-400 text-sm">Keranjang kosong.</p>
                        <button onclick="switchTab('home')" class="mt-4 text-blue-600 font-bold text-xs">Mulai Belanja</button>
                    </div>
                 `;
                 if(checkoutSec) checkoutSec.classList.add('hidden');
             } else {
                 renderCartItems();
                 if(checkoutSec) checkoutSec.classList.remove('hidden');
             }
             calculateTotal();

         } catch (e) {
             console.error(e);
             container.innerHTML = '<p class="text-center text-red-500 text-sm">Gagal memuat keranjang.</p>';
         }
    }

    function renderCartItems() {
        const container = document.getElementById('cart-items-container');
        container.innerHTML = '';

        cartItemsData.forEach((item, index) => {
            const priceFormatted = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(item.product.price);
            
            const div = document.createElement('div');
            div.className = "bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-col md:flex-row gap-4 relative";
            
            div.innerHTML = `
                <!-- Product Img -->
                <div class="w-full md:w-24 h-24 bg-gray-100 rounded-lg flex-shrink-0 overflow-hidden">
                    <img src="${item.product.image_url}" class="w-full h-full object-cover">
                </div>
                
                <!-- Details -->
                <div class="flex-1">
                    <h4 class="font-bold text-gray-900 text-sm line-clamp-2 mb-1">${item.product.name}</h4>
                    <p class="text-orange-500 font-bold text-sm mb-3">${priceFormatted}</p>
                    
                    <!-- Controls -->
                    <div class="flex items-center justify-between">
                         <!-- Qty Stepper -->
                         <div class="flex items-center space-x-3 bg-gray-50 rounded-lg p-1">
                            <button onclick="updateQty(${index}, -1)" class="w-6 h-6 flex items-center justify-center bg-white rounded shadow-sm text-gray-600 hover:text-red-500 font-bold">-</button>
                            <span class="text-sm font-bold w-4 text-center">${item.quantity}</span>
                            <button onclick="updateQty(${index}, 1)" class="w-6 h-6 flex items-center justify-center bg-white rounded shadow-sm text-gray-600 hover:text-green-500 font-bold">+</button>
                         </div>

                         <!-- Delete -->
                         <button onclick="removeCartItem(${item.id})" class="text-gray-400 hover:text-red-500 p-2">
                            @svg('heroicon-o-trash', 'w-5 h-5')
                         </button>
                    </div>

                    <!-- Note / Variant Input -->
                    <div class="mt-3">
                        <input type="text" 
                               id="note-${index}" 
                               value="${item.note || ''}" 
                               onchange="updateNote(${index}, this.value)"
                               placeholder="Tulis Catatan / Ukuran (Opsional)..." 
                               class="w-full text-xs border border-gray-200 rounded-lg p-2 bg-gray-50 focus:border-blue-400 outline-none">
                    </div>
                </div>
            `;
            container.appendChild(div);
        });
    }

    function updateQty(index, change) {
        let item = cartItemsData[index];
        let newQty = item.quantity + change;
        if(newQty < 1) return;

        item.quantity = newQty;
        renderCartItems(); // Re-render to show new qty
        calculateTotal();
    }

    function updateNote(index, val) {
        cartItemsData[index].note = val;
    }

    function calculateTotal() {
        let total = 0;
        let count = 0;
        cartItemsData.forEach(item => {
            total += item.product.price * item.quantity;
            count += item.quantity;
        });

        document.getElementById('checkout-total-items').innerText = count;
        document.getElementById('checkout-total-price').innerText = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(total);
    }

    async function removeCartItem(id) {
        if(!confirm("Hapus item ini?")) return;
        
        // Optimistic UI update
        cartItemsData = cartItemsData.filter(i => i.id !== id);
        renderCartItems();
        calculateTotal();
        if(cartItemsData.length === 0) {
             document.getElementById('cart-items-container').innerHTML = `
                <div class="text-center py-10 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                    <p class="text-gray-400 text-sm">Keranjang kosong.</p>
                </div>`;
             document.getElementById('checkout-section').classList.add('hidden');
        }

        // Call Backend to delete
        try {
            const token = await getFreshToken();
             await fetch('/cart/remove', { // Need this route too
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                body: JSON.stringify({ id: id })
            });
            window.updateCartBadge(); // Update badge
        } catch(e) { console.error(e); }
    }

    async function processCheckout() {
        const address = document.getElementById('checkout-address').value;
        if(!address.trim()) {
            alert("Mohon isi alamat pengiriman.");
            return;
        }

        const btn = document.getElementById('btn-checkout');
        btn.disabled = true;
        btn.innerText = "Memproses...";

        try {
            const token = await getFreshToken();
            const response = await fetch('/order/checkout', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                body: JSON.stringify({
                    address: address,
                    items: cartItemsData.map(i => ({
                        id: i.id,
                        quantity: i.quantity,
                        note: i.note
                    }))
                })
            });

            const data = await response.json();
            
            if(response.ok) {
                window.location.href = data.redirect_url;
            } else {
                alert("Gagal: " + (data.message || 'Error'));
                btn.disabled = false;
                btn.innerText = "Checkout";
            }

        } catch(e) {
            console.error(e);
            alert("Terjadi kesalahan koneksi.");
            btn.disabled = false;
             btn.innerText = "Checkout";
        }
    }
</script>
