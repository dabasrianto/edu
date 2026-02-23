
            <!-- ======================= -->
            <!-- TAB: LOGIN              -->
            <!-- ======================= -->
            <div id="view-login" class="tab-content hidden fade-in py-10">
                <div class="text-center mb-10">
                    <div class="w-20 h-20 bg-blue-900 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg overflow-hidden border-4 border-white">
                         @if($appSettings->logo_path)
                            <img src="{{ Storage::url($appSettings->logo_path) }}" class="w-full h-full object-cover">
                         @else
                            <span class="text-3xl text-white font-bold">{{ substr($appSettings->app_name ?? 'Edu HSI', 0, 2) }}</span>
                         @endif
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $appSettings->app_name ?? 'Edu HSI' }}</h2>
                    <p class="text-gray-500 text-sm mt-1">{{ $appSettings->app_slogan ?? 'Belajar Kapanpun, Dimanapun' }}</p>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mx-4" id="login-form-container">
                    <h3 class="font-bold text-lg mb-4 text-center">{{ $appSettings->login_header_text ?? 'Masuk Akun' }}</h3>
                    
                    @if($errors->any() || session('error'))
                        <div class="bg-red-50 text-red-600 p-3 rounded-lg text-xs mb-4">
                            Details: {{ $errors->first() ?? session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('login') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1 ml-1">Email / NIP</label>
                            <input type="email" name="email" required placeholder="user@gmail.com"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 focus:bg-white transition-colors" value="{{ old('email') }}">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1 ml-1">Password</label>
                            <input type="password" name="password" required placeholder="••••••••"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 focus:bg-white transition-colors">
                        </div>
                        <button type="submit"
                            class="w-full bg-blue-900 text-white font-bold py-3.5 rounded-xl hover:bg-blue-800 transition-colors shadow-lg active:scale-95 transform mt-2">
                            Masuk Sekarang
                        </button>
                    </form>

                    
                    <div class="mt-6 text-center">
                        <p class="text-xs text-gray-400">Belum punya akun?</p>
                        <button onclick="toggleAuthMode()" class="text-blue-600 font-bold text-sm hover:underline mt-1">Daftar Akun Baru</button>
                    </div>
                </div>

                <div class="hidden bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mx-4" id="register-form-container">
                     <h3 class="font-bold text-lg mb-4 text-center">Daftar Akun</h3>
                     
                     <form action="{{ route('register') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1 ml-1">Nama Lengkap</label>
                            <input type="text" name="name" required placeholder="Fulan bin Fulan"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 focus:bg-white transition-colors">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1 ml-1">Email</label>
                            <input type="email" name="email" required placeholder="email@contoh.com"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 focus:bg-white transition-colors">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1 ml-1">Password</label>
                            <input type="password" name="password" required placeholder="Minimal 8 karakter"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 focus:bg-white transition-colors">
                        </div>
                         <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1 ml-1">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" required placeholder="Ulangi password"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 focus:bg-white transition-colors">
                        </div>
                        <button type="submit"
                            class="w-full bg-blue-900 text-white font-bold py-3.5 rounded-xl hover:bg-blue-800 transition-colors shadow-lg active:scale-95 transform mt-2">
                            Daftar Sekarang
                        </button>
                    </form>
                    
                    <div class="mt-6 text-center">
                        <p class="text-xs text-gray-400">Sudah punya akun?</p>
                        <button onclick="toggleAuthMode()" class="text-blue-600 font-bold text-sm hover:underline mt-1">Masuk Disini</button>
                    </div>
                </div>

                <script>
                    function toggleAuthMode() {
                        const loginForm = document.getElementById('login-form-container');
                        const regForm = document.getElementById('register-form-container');
                        
                        loginForm.classList.toggle('hidden');
                        regForm.classList.toggle('hidden');
                    }
                </script>
            </div>
