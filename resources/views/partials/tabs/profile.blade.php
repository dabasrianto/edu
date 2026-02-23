
            <!-- ======================= -->
            <!-- TAB: PROFIL             -->
            <!-- ======================= -->
            <div id="view-profil" class="tab-content {{ request('tab') == 'profil' ? '' : 'hidden' }} fade-in">
                @auth
                <!-- Header Profil -->
                <div class="bg-blue-900 text-white p-6 pb-20 relative overflow-hidden">
                    <div class="relative z-10 flex items-center space-x-4">
                        <div class="w-16 h-16 rounded-full border-2 border-white/30 bg-white/10 flex items-center justify-center text-xl font-bold overflow-hidden">
                            @if(Auth::user()->avatar)
                                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" class="w-full h-full object-cover">
                            @else
                                {{ substr(Auth::user()->name, 0, 2) }}
                            @endif
                        </div>
                        <div>
                            <h2 class="text-xl font-bold">{{ Auth::user()->name }}</h2>
                            <p class="text-blue-200 text-xs">{{ Auth::user()->email }}</p>
                            <span class="inline-block mt-2 bg-blue-800 px-2 py-0.5 rounded text-[10px] border border-blue-700">Member Aktif</span>
                        </div>
                    </div>
                    <!-- Stats Ringan -->
                    <div class="absolute right-0 top-0 bottom-0 w-1/3 bg-blue-800/30 skew-x-12 transform translate-x-4"></div>
                </div>

                <!-- Floating Card Saldo -->
                <div class="bg-white rounded-xl shadow-md mx-4 -mt-10 mb-6 p-4 border border-gray-100 relative z-20">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Total Saldo Dompet</p>
                            <h3 class="text-2xl font-bold text-gray-900">Rp {{ number_format(Auth::user()->balance ?? 0, 0, ',', '.') }}</h3>
                        </div>
                        <div class="bg-blue-50 p-2 rounded-lg">
                            @svg('heroicon-s-wallet', 'w-6 h-6 text-blue-600')
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button onclick="toggleModal('topup-modal')" class="flex-1 bg-blue-900 text-white py-2 rounded-lg text-xs font-bold hover:bg-blue-800 transition-colors flex items-center justify-center">
                            @svg('heroicon-s-plus', 'w-4 h-4 mr-1') Top Up
                        </button>
                        <button onclick="toggleModal('history-modal')" class="flex-1 bg-gray-50 text-gray-700 py-2 rounded-lg text-xs font-bold hover:bg-gray-100 transition-colors flex items-center justify-center">
                            @svg('heroicon-s-clock', 'w-4 h-4 mr-1') Riwayat
                        </button>
                    </div>
                </div>

                <!-- Modal Top Up (Moved to partials/modals/topup.blade.php) -->


                <!-- Menu Profil -->
                <div class="px-4 space-y-3 pb-20">
                    <h3 class="font-bold text-gray-900 text-sm ml-1">Akun</h3>
                    
                    <button onclick="toggleModal('edit-profile-modal')" class="w-full bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center hover:bg-gray-50 transition-colors group">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-blue-50 rounded-lg text-blue-600 group-hover:bg-blue-100 transition-colors">
                                @svg('heroicon-s-user', 'w-5 h-5')
                            </div>
                            <span class="text-sm font-medium text-gray-700">Edit Profil</span>
                        </div>
                        @svg('heroicon-o-chevron-right', 'w-4 h-4 text-gray-400')
                    </button>

                     <a href="{{ route('profile.orders') }}" class="w-full bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center hover:bg-gray-50 transition-colors group">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-purple-50 rounded-lg text-purple-600 group-hover:bg-purple-100 transition-colors">
                                @svg('heroicon-s-shopping-bag', 'w-5 h-5')
                            </div>
                            <span class="text-sm font-medium text-gray-700">Orderan Saya</span>
                        </div>
                        @svg('heroicon-o-chevron-right', 'w-4 h-4 text-gray-400')
                    </a>

                    <a href="{{ route('profile.chats') }}" class="w-full bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center hover:bg-gray-50 transition-colors group">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-green-50 rounded-lg text-green-600 group-hover:bg-green-100 transition-colors">
                                @svg('heroicon-s-chat-bubble-left-right', 'w-5 h-5')
                            </div>
                            <span class="text-sm font-medium text-gray-700">Riwayat Chat</span>
                        </div>
                        @svg('heroicon-o-chevron-right', 'w-4 h-4 text-gray-400')
                    </a>

                     <button onclick="toggleModal('change-password-modal')" class="w-full bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center hover:bg-gray-50 transition-colors group">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-orange-50 rounded-lg text-orange-600 group-hover:bg-orange-100 transition-colors">
                                @svg('heroicon-s-key', 'w-5 h-5')
                            </div>
                            <span class="text-sm font-medium text-gray-700">Ganti Password</span>
                        </div>
                        @svg('heroicon-o-chevron-right', 'w-4 h-4 text-gray-400')
                    </button>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center hover:bg-red-50 group mt-3 transition-colors">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-red-50 rounded-lg text-red-600 group-hover:bg-red-100 transition-colors">
                                    @svg('heroicon-s-arrow-left-on-rectangle', 'w-5 h-5')
                                </div>
                                <span class="text-sm font-medium text-red-600">Keluar Aplikasi</span>
                            </div>
                        </button>
                    </form>
                </div>

                <!-- Modal Riwayat Transaksi -->
                <div id="history-modal" onclick="if(event.target === this) toggleModal('history-modal')" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm transition-opacity">
                    <div class="bg-white rounded-2xl w-full max-w-sm p-6 animate-scale-up max-h-[70vh] flex flex-col shadow-2xl relative">
                        <div class="flex justify-between items-center mb-4 flex-shrink-0">
                            <h3 class="font-bold text-gray-900">Riwayat Transaksi</h3>
                            <button onclick="toggleModal('history-modal')" class="text-gray-400 hover:text-red-500">@svg('heroicon-o-x-mark', 'w-6 h-6')</button>
                        </div>
                        
                        <div class="overflow-y-auto flex-grow -mx-6 px-6 space-y-3">
                            @if(isset($depositHistory) && count($depositHistory) > 0)
                                @foreach($depositHistory as $deposit)
                                <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <p class="text-xs font-bold text-gray-900">Top Up Saldo</p>
                                            <p class="text-[10px] text-gray-500">{{ $deposit->created_at->format('d M Y H:i') }}</p>
                                        </div>
                                        @php
                                            $statusColor = match($deposit->status) {
                                                'pending' => 'bg-orange-100 text-orange-700',
                                                'approved' => 'bg-green-100 text-green-700',
                                                'rejected' => 'bg-red-100 text-red-700',
                                                default => 'bg-gray-100 text-gray-700'
                                            };
                                            $statusLabel = match($deposit->status) {
                                                'pending' => 'Menunggu',
                                                'approved' => 'Berhasil',
                                                'rejected' => 'Ditolak',
                                                default => ucfirst($deposit->status)
                                            };
                                        @endphp
                                        <span class="text-[10px] px-2 py-0.5 rounded-full font-bold {{ $statusColor }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </div>
                                    
                                    <div class="flex justify-between items-center">
                                        <p class="text-blue-700 font-bold text-sm">Rp {{ number_format($deposit->amount, 0, ',', '.') }}</p>
                                        
                                        @if($deposit->bankAccount)
                                            <span class="text-[10px] text-gray-400 bg-white px-1.5 py-0.5 rounded border border-gray-200">
                                                {{ $deposit->bankAccount->bank_name }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    @if($deposit->admin_note)
                                        <div class="mt-2 text-[10px] text-gray-500 bg-white p-2 rounded border border-gray-200 italic">
                                            Note: {{ $deposit->admin_note }}
                                        </div>
                                    @endif
                                </div>
                                @endforeach
                            @else
                                <div class="text-center py-10 text-gray-400">
                                    @svg('heroicon-o-clock', 'w-10 h-10 mx-auto mb-2 text-gray-300')
                                    <p class="text-xs">Belum ada riwayat transaksi</p>
                                </div>
                            @endif
                @if($errors->has('amount') || $errors->has('proof_image') || $errors->has('bank_account_id'))
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        // Ensure Profile Tab is Active
                                        switchTab('profil');
                                        // Open TopUp Modal
                                        document.getElementById('topup-modal').classList.remove('hidden');
                                    });
                                </script>
                            @endif
                        </div>
                    </div>
                </div>

                <script>
                    function previewEditAvatar(input) {
                        if (input.files && input.files[0]) {
                            var reader = new FileReader();
                            reader.onload = function(e) {
                                // Find the img element in the previous sibling container or within the form
                                // Currently structure: div > img
                                var img = input.parentElement.querySelector('img');
                                if(img) {
                                    img.src = e.target.result;
                                } else {
                                    // If placeholder div exists instead of img
                                    var container = input.parentElement;
                                    var placeholder = container.querySelector('.bg-blue-100');
                                    if(placeholder) {
                                        placeholder.classList.add('hidden');
                                        var newImg = document.createElement('img');
                                        newImg.src = e.target.result;
                                        newImg.className = "w-20 h-20 rounded-full object-cover border-2 border-gray-200";
                                        container.insertBefore(newImg, container.firstChild);
                                    }
                                }
                            }
                            reader.readAsDataURL(input.files[0]);
                        }
                    }
                </script>

                 <!-- Modals for Profile Actions -->
                 <!-- Modal Edit Profile -->
                <div id="edit-profile-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
                    <div class="bg-white rounded-2xl w-full max-w-sm p-6 animate-scale-up">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-bold text-gray-900">Edit Profil</h3>
                            <button onclick="toggleModal('edit-profile-modal')" class="text-gray-400 hover:text-red-500">@svg('heroicon-o-x-mark', 'w-6 h-6')</button>
                        </div>
                        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            @method('PUT')
                            <!-- Avatar Preview in Edit -->
                            <div class="flex justify-center mb-4">
                                <div class="relative w-20 h-20">
                                    @if(Auth::user()->avatar)
                                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}" class="w-20 h-20 rounded-full object-cover border-2 border-gray-200">
                                    @else
                                        <div class="w-20 h-20 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-2xl">
                                            {{ substr(Auth::user()->name, 0, 2) }}
                                        </div>
                                    @endif
                                    <label for="avatar-upload" class="absolute bottom-0 right-0 bg-blue-900 text-white p-1 rounded-full cursor-pointer shadow-sm hover:bg-blue-800">
                                        @svg('heroicon-s-camera', 'w-4 h-4')
                                    </label>
                                    <input type="file" id="avatar-upload" name="avatar" class="hidden" accept="image/*" onchange="previewEditAvatar(this)">
                                </div>
                            </div>
                            
                            <div>
                                <label class="text-xs font-bold text-gray-700 block mb-1">Nama Lengkap</label>
                                <input type="text" name="name" value="{{ Auth::user()->name }}" class="w-full border border-gray-200 rounded-lg p-2.5 text-sm focus:border-blue-500 outline-none">
                            </div>
                            <div>
                                <label class="text-xs font-bold text-gray-700 block mb-1">Email</label>
                                <input type="email" name="email" value="{{ Auth::user()->email }}" class="w-full border border-gray-200 rounded-lg p-2.5 text-sm focus:border-blue-500 outline-none">
                            </div>
                            <div>
                                <label class="text-xs font-bold text-gray-700 block mb-1">NIP (Nomor Induk Peserta)</label>
                                <input type="text" name="nip" value="{{ Auth::user()->nip }}" placeholder="Masukkan NIP Anda" class="w-full border border-gray-200 rounded-lg p-2.5 text-sm focus:border-blue-500 outline-none">
                            </div>
                            <button type="submit" class="w-full bg-blue-900 text-white font-bold py-2.5 rounded-lg hover:bg-blue-800">Simpan Perubahan</button>
                        </form>
                    </div>
                </div>

                <!-- Modal Ganti Password -->
                <div id="change-password-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
                    <div class="bg-white rounded-2xl w-full max-w-sm p-6 animate-scale-up">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-bold text-gray-900">Ganti Password</h3>
                            <button onclick="toggleModal('change-password-modal')" class="text-gray-400 hover:text-red-500">@svg('heroicon-o-x-mark', 'w-6 h-6')</button>
                        </div>
                        <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PUT')
                            <div>
                                <label class="text-xs font-bold text-gray-700 block mb-1">Password Saat Ini</label>
                                <input type="password" name="current_password" class="w-full border border-gray-200 rounded-lg p-2.5 text-sm focus:border-blue-500 outline-none">
                            </div>
                            <div>
                                <label class="text-xs font-bold text-gray-700 block mb-1">Password Baru</label>
                                <input type="password" name="password" class="w-full border border-gray-200 rounded-lg p-2.5 text-sm focus:border-blue-500 outline-none">
                            </div>
                            <div>
                                <label class="text-xs font-bold text-gray-700 block mb-1">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" class="w-full border border-gray-200 rounded-lg p-2.5 text-sm focus:border-blue-500 outline-none">
                            </div>
                            <button type="submit" class="w-full bg-blue-900 text-white font-bold py-2.5 rounded-lg hover:bg-blue-800">Update Password</button>
                        </form>
                    </div>
                </div>

                @else
                <div class="flex flex-col items-center justify-center h-full text-center p-8 bg-gray-50 rounded-2xl border border-dashed border-gray-200 m-4">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        @svg('heroicon-s-user', 'w-8 h-8 text-gray-400')
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Belum Login</h3>
                    <p class="text-gray-500 text-sm mb-6">Silakan login untuk mengakses profil dan data Anda.</p>
                    <button onclick="switchTab('login')" class="bg-blue-900 text-white px-6 py-2 rounded-lg font-bold shadow-lg hover:bg-blue-800 transition-colors">
                        Login Sekarang
                    </button>
                </div>
                @endauth
            </div>
