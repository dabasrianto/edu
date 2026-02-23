
    <!-- ORDER PAYMENT MODAL -->
    <div id="order-payment-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" onclick="closeOrderPaymentModal()"></div>
        <div class="relative bg-white rounded-2xl max-w-md w-full p-6 animate-scale-up z-10">
            <button onclick="closeOrderPaymentModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                @svg('heroicon-o-x-mark', 'w-6 h-6')
            </button>
            <h3 class="text-xl font-bold mb-1 text-gray-900">Pembayaran Orderan</h3>
            <p class="text-xs text-gray-500 mb-6">Pilih metode pembayaran untuk menyelesaikan pesanan.</p>

            <!-- Tabs Metode Pembayaran -->
            <div class="flex space-x-2 mb-4 border-b border-gray-100 pb-2">
                <button onclick="switchOrderPaymentTab('balance')" id="tab-order-balance" class="flex-1 py-2 text-sm font-bold text-blue-900 border-b-2 border-blue-900 transition-colors">
                    Saldo Dompet
                </button>
                <button onclick="switchOrderPaymentTab('transfer')" id="tab-order-transfer" class="flex-1 py-2 text-sm font-bold text-gray-400 border-b-2 border-transparent hover:text-gray-600 transition-colors">
                    Transfer Bank
                </button>
            </div>

            <!-- TAB 1: SALDO DOMPET -->
            <div id="content-order-balance" class="space-y-4">
                <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 flex justify-between items-center">
                    <div>
                        <p class="text-xs text-blue-600 mb-1">Saldo Anda Saat Ini</p>
                        <h4 class="text-xl font-bold text-blue-900">Rp {{ number_format(Auth::user()->balance ?? 0, 0, ',', '.') }}</h4>
                    </div>
                    @svg('heroicon-s-wallet', 'w-8 h-8 text-blue-300')
                </div>

                <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                    <div class="flex justify-between items-center mb-2">
                         <span class="text-sm text-gray-600">Total Tagihan</span>
                         <span class="text-sm font-bold text-gray-900" id="order-balance-price">Rp 0</span>
                    </div>
                    <div class="flex justify-between items-center border-t border-gray-200 pt-2">
                         <span class="text-sm font-bold text-gray-900">Sisa Saldo Nanti</span>
                         <span class="text-sm font-bold text-green-600" id="order-balance-remaining">Rp 0</span>
                    </div>
                </div>
                
                <div id="order-balance-warning" class="hidden bg-red-50 text-red-600 p-3 rounded-lg text-xs flex items-center">
                    @svg('heroicon-s-exclamation-circle', 'w-4 h-4 mr-2 flex-shrink-0')
                    Saldo tidak mencukupi. Silakan Top Up terlebih dahulu.
                </div>

                <button id="btn-pay-order-balance" onclick="processOrderBalancePayment()" class="w-full bg-blue-900 text-white font-bold py-3 rounded-xl hover:bg-blue-800 transition-colors shadow-lg active:scale-95 transform">
                    Bayar Sekarang
                </button>
            </div>

            <!-- TAB 2: TRANSFER BANK -->
            <div id="content-order-transfer" class="hidden space-y-4">
                 <div class="bg-orange-50 p-4 rounded-xl border border-orange-100">
                    <p class="text-xs text-orange-800 mb-2 leading-relaxed">
                        @php
                            $payInstruksi = $appSettings->payment_config['instruction_text'] ?? 'Silakan transfer sebesar nominal tagihan ke rekening admin, lalu upload bukti pembayarannya di sini.';
                            $payWa = $appSettings->payment_config['whatsapp_number'] ?? '6281234567890';
                        @endphp
                        {{ $payInstruksi }}
                        <br><br>
                        <strong>Total: <span id="order-transfer-price">Rp 0</span></strong>
                    </p>
                    <button onclick="redirectToWA('{{ $payWa }}')" class="text-xs bg-green-500 text-white px-3 py-1.5 rounded-lg font-bold hover:bg-green-600 flex items-center w-max">
                        @svg('heroicon-s-chat-bubble-left-right', 'w-4 h-4 mr-1')
                        Chat Admin (WA)
                    </button>
                </div>

                <form id="order-payment-form" onsubmit="handleOrderPaymentSubmit(event)" class="space-y-4">
                    @csrf
                    <input type="hidden" name="order_id" id="payment-order-id">
                    
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:bg-gray-50 transition-colors cursor-pointer relative" onclick="document.getElementById('order-proof-input').click()">
                        <input type="file" name="payment_proof" id="order-proof-input" class="hidden" accept="image/*" onchange="previewOrderPaymentImage(this)" required>
                        
                        <div id="order-upload-placeholder">
                            @svg('heroicon-o-cloud-arrow-up', 'w-10 h-10 text-gray-400 mx-auto mb-2')
                            <p class="text-xs text-gray-500 font-bold">Klik untuk Upload Bukti</p>
                            <p class="text-[10px] text-gray-400">JPG, PNG (Max 2MB)</p>
                        </div>
                        <div id="order-upload-preview" class="hidden">
                             <img src="" class="max-h-32 mx-auto rounded-lg shadow-sm">
                             <p class="text-[10px] text-blue-600 mt-2 font-bold">Ganti Gambar</p>
                        </div>
                    </div>

                    <button type="submit" id="btn-submit-order-payment" class="w-full bg-green-600 text-white font-bold py-3 rounded-xl hover:bg-green-700 transition-colors shadow-lg active:scale-95 transform">
                        Kirim Bukti Pembayaran
                    </button>
                </form>
            </div>
            
            <script>
                let paymentOrder = null;

                function openOrderPaymentModal(order) {
                    paymentOrder = order;
                    document.getElementById('payment-order-id').value = order.id;
                    
                    // Reset View
                    switchOrderPaymentTab('balance');
                    document.getElementById('order-payment-modal').classList.remove('hidden');
                    
                    // Trigger calc
                    checkOrderBalanceSufficiency(order.total_amount);
                }

                function closeOrderPaymentModal() {
                    document.getElementById('order-payment-modal').classList.add('hidden');
                }

                function previewOrderPaymentImage(input) {
                    if (input.files && input.files[0]) {
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            document.getElementById('order-upload-placeholder').classList.add('hidden');
                            const preview = document.getElementById('order-upload-preview');
                            preview.classList.remove('hidden');
                            preview.querySelector('img').src = e.target.result;
                        }
                        reader.readAsDataURL(input.files[0]);
                    }
                }

                function switchOrderPaymentTab(tab) {
                    const balanceContent = document.getElementById('content-order-balance');
                    const transferContent = document.getElementById('content-order-transfer');
                    
                    const tabBalance = document.getElementById('tab-order-balance');
                    const tabTransfer = document.getElementById('tab-order-transfer');
                    
                    if (tab === 'balance') {
                        balanceContent.classList.remove('hidden');
                        transferContent.classList.add('hidden');
                        
                        tabBalance.classList.add('text-blue-900', 'border-blue-900');
                        tabBalance.classList.remove('text-gray-400', 'border-transparent');
                        
                        tabTransfer.classList.add('text-gray-400', 'border-transparent');
                        tabTransfer.classList.remove('text-blue-900', 'border-blue-900');
                        
                        if(paymentOrder) {
                            checkOrderBalanceSufficiency(paymentOrder.total_amount);
                        }
                    } else {
                        balanceContent.classList.add('hidden');
                        transferContent.classList.remove('hidden');
                        
                        tabTransfer.classList.add('text-blue-900', 'border-blue-900');
                        tabTransfer.classList.remove('text-gray-400', 'border-transparent');
                        
                        tabBalance.classList.add('text-gray-400', 'border-transparent');
                        tabBalance.classList.remove('text-blue-900', 'border-blue-900');
                    }
                }
                
                function checkOrderBalanceSufficiency(price) {
                    const currentBalance = {{ Auth::check() ? Auth::user()->balance ?? 0 : 0 }};
                    const remaining = currentBalance - price;
                    
                    const priceEl = document.getElementById('order-balance-price');
                    const remainEl = document.getElementById('order-balance-remaining');
                    const warningEl = document.getElementById('order-balance-warning');
                    const btnPay = document.getElementById('btn-pay-order-balance');
                    
                    priceEl.innerText = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(price);
                    remainEl.innerText = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(remaining);
                    
                    if (remaining < 0) {
                        remainEl.classList.add('text-red-600');
                        remainEl.classList.remove('text-green-600');
                        warningEl.classList.remove('hidden');
                        btnPay.disabled = true;
                        btnPay.classList.add('opacity-50', 'cursor-not-allowed');
                    } else {
                        remainEl.classList.add('text-green-600');
                        remainEl.classList.remove('text-red-600');
                        warningEl.classList.add('hidden');
                        btnPay.disabled = false;
                        btnPay.classList.remove('opacity-50', 'cursor-not-allowed');
                    }
                    
                    // Update Transfer Price too
                    document.getElementById('order-transfer-price').innerText = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(price);
                }
                
                async function processOrderBalancePayment() {
                    if(!paymentOrder) return;
                    
                    const btn = document.getElementById('btn-pay-order-balance');
                    if(btn.disabled) return;
                    
                    if(!confirm('Anda yakin ingin membayar orderan ini dengan saldo dompet?')) return;
                    
                    btn.disabled = true;
                    btn.innerText = "Memproses...";
                    
                    try {
                        const token = document.querySelector('input[name="_token"]').value;
                        const response = await fetch('/order/pay-balance', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token
                            },
                            body: JSON.stringify({ order_id: paymentOrder.id })
                        });
                        
                        const data = await response.json();
                        
                        if(response.ok) {
                            alert("Pembayaran Berhasil! Pesanan sedang diproses.");
                            closeOrderPaymentModal();
                            location.reload(); 
                        } else {
                            alert("Gagal: " + (data.message || 'Terjadi kesalahan server'));
                            btn.disabled = false;
                            btn.innerText = "Bayar Sekarang";
                        }
                    } catch(e) {
                         alert("Kesalahan jaringan.");
                         btn.disabled = false;
                         btn.innerText = "Bayar Sekarang";
                    }
                }

                async function handleOrderPaymentSubmit(e) {
                    e.preventDefault();
                    
                    const form = document.getElementById('order-payment-form');
                    const btn = document.getElementById('btn-submit-order-payment');
                    
                    btn.disabled = true;
                    btn.innerText = "Mengirim...";
                    
                    const formData = new FormData(form);
                    
                    try {
                        const response = await fetch('/order/payment', {
                            method: 'POST',
                            body: formData
                        });
                        
                        const data = await response.json();
                        
                        if(response.ok) {
                            alert("Bukti pembayaran berhasil dikirim. Admin akan segera memverifikasi.");
                            closeOrderPaymentModal();
                            location.reload();
                        } else {
                            alert("Gagal: " + (data.message || 'Terjadi kesalahan server'));
                            btn.disabled = false;
                            btn.innerText = "Kirim Bukti Pembayaran";
                        }
                    } catch(e) {
                         console.error(e);
                         alert("Terjadi kesalahan jaringan.");
                         btn.disabled = false;
                         btn.innerText = "Kirim Bukti Pembayaran";
                    }
                }
            </script>
        </div>
    </div>
