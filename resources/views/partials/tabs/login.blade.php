
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

                <div class="hidden bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mx-4" id="otp-form-container">
                    <div class="text-center mb-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2 text-blue-600">
                            @svg('heroicon-s-shield-check', 'w-6 h-6')
                        </div>
                        <h3 class="font-bold text-lg">Verifikasi OTP</h3>
                        <p class="text-xs text-gray-500 mt-1">Masukkan 6 digit kode yang dikirim ke <span id="otp-email-display" class="font-bold text-gray-700"></span></p>
                    </div>
                    
                    <div id="otp-error" class="hidden bg-red-50 text-red-600 p-3 rounded-lg text-xs mb-4"></div>

                    <form id="otp-form" class="space-y-4">
                        @csrf
                        <input type="hidden" id="otp-email" name="email">
                        <input type="hidden" id="otp-action" name="action">
                        <div>
                            <input type="text" id="otp-code" name="otp" required maxlength="6" placeholder="000000"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-4 text-center text-2xl font-bold tracking-[0.5em] focus:outline-none focus:border-blue-500 focus:bg-white transition-colors">
                        </div>
                        <button type="submit" id="otp-submit-btn"
                            class="w-full bg-blue-900 text-white font-bold py-3.5 rounded-xl hover:bg-blue-800 transition-colors shadow-lg active:scale-95 transform">
                            Verifikasi Sekarang
                        </button>
                    </form>

                    <div class="mt-6 text-center">
                        <p class="text-xs text-gray-400">Tidak menerima kode?</p>
                        <button onclick="resendOTP()" id="resend-btn" class="text-blue-600 font-bold text-sm hover:underline mt-1">Kirim Ulang Kode</button>
                    </div>
                    <button onclick="cancelOTP()" class="w-full text-xs text-gray-400 hover:text-gray-600 mt-4">Batal</button>
                </div>

                <script>
                    function toggleAuthMode() {
                        const loginForm = document.getElementById('login-form-container');
                        const regForm = document.getElementById('register-form-container');
                        const otpForm = document.getElementById('otp-form-container');
                        
                        loginForm.classList.remove('hidden');
                        regForm.classList.add('hidden');
                        otpForm.classList.add('hidden');
                        
                        if (!loginForm.classList.contains('hidden')) {
                            // Already in login, switch to reg
                            loginForm.classList.add('hidden');
                            regForm.classList.remove('hidden');
                        } else {
                            // In reg, switch to login
                            loginForm.classList.remove('hidden');
                            regForm.classList.add('hidden');
                        }
                    }

                    function showOTPForm(email, action) {
                        document.getElementById('login-form-container').classList.add('hidden');
                        document.getElementById('register-form-container').classList.add('hidden');
                        document.getElementById('otp-form-container').classList.remove('hidden');
                        document.getElementById('otp-email').value = email;
                        document.getElementById('otp-email-display').innerText = email;
                        document.getElementById('otp-action').value = action;
                        document.getElementById('otp-error').classList.add('hidden');
                        document.getElementById('otp-code').value = '';
                    }

                    function cancelOTP() {
                        document.getElementById('otp-form-container').classList.add('hidden');
                        document.getElementById('login-form-container').classList.remove('hidden');
                    }

                    // Handle Login Form AJAX
                    document.querySelector('#login-form-container form').addEventListener('submit', function(e) {
                        e.preventDefault();
                        const btn = e.target.querySelector('button');
                        const originalText = btn.innerText;
                        btn.innerText = 'Memproses...';
                        btn.disabled = true;

                        const formData = new FormData(this);
                        
                        fetch("{{ route('login') }}", {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.requires_otp) {
                                showOTPForm(data.email, 'login');
                            } else if (data.success) {
                                window.location.href = data.redirect || '/?tab=profil';
                            } else {
                                alert(data.message || 'Login gagal.');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            alert('Terjadi kesalahan pada server.');
                        })
                        .finally(() => {
                            btn.innerText = originalText;
                            btn.disabled = false;
                        });
                    });

                    // Handle Register Form AJAX
                    document.querySelector('#register-form-container form').addEventListener('submit', function(e) {
                        e.preventDefault();
                        const btn = e.target.querySelector('button');
                        const originalText = btn.innerText;
                        btn.innerText = 'Mendaftar...';
                        btn.disabled = true;

                        const formData = new FormData(this);
                        
                        fetch("{{ route('register.store') }}", {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.requires_otp) {
                                showOTPForm(data.email, 'register');
                            } else if (data.success) {
                                window.location.href = data.redirect || '/?tab=profil';
                            } else {
                                alert(data.message || 'Registrasi gagal.');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            alert('Terjadi kesalahan pada server.');
                        })
                        .finally(() => {
                            btn.innerText = originalText;
                            btn.disabled = false;
                        });
                    });

                    // Handle OTP Form AJAX
                    document.getElementById('otp-form').addEventListener('submit', function(e) {
                        e.preventDefault();
                        const btn = document.getElementById('otp-submit-btn');
                        const originalText = btn.innerText;
                        btn.innerText = 'Memverifikasi...';
                        btn.disabled = true;

                        const formData = new FormData(this);
                        
                        fetch("{{ route('otp.verify') }}", {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                window.location.href = data.redirect;
                            } else {
                                const errorEl = document.getElementById('otp-error');
                                errorEl.innerText = data.message;
                                errorEl.classList.remove('hidden');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            alert('Terjadi kesalahan pada verifikasi.');
                        })
                        .finally(() => {
                            btn.innerText = originalText;
                            btn.disabled = false;
                        });
                    });

                    function resendOTP() {
                        const email = document.getElementById('otp-email').value;
                        const btn = document.getElementById('resend-btn');
                        const originalText = btn.innerText;
                        btn.innerText = 'Mengirim...';
                        btn.disabled = true;

                        fetch("{{ route('otp.resend') }}", {
                            method: 'POST',
                            body: JSON.stringify({ email: email }),
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            alert(data.message);
                        })
                        .catch(err => {
                            console.error(err);
                            alert('Gagal mengirim ulang OTP.');
                        })
                        .finally(() => {
                            btn.innerText = originalText;
                            btn.disabled = false;
                        });
                    }
                </script>
            </div>
