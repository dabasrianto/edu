<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100">
<div class="min-h-screen flex flex-col items-center justify-center bg-white">
    <!-- Logo & Branding -->
    <div class="text-center mb-8">
        <div class="w-20 h-20 bg-blue-900 rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4 overflow-hidden shadow-lg border-4 border-white">
             @if($appSettings->logo_path)
                <img src="{{ Storage::url($appSettings->logo_path) }}" class="w-full h-full object-cover">
             @else
                {{ substr($appSettings->app_name ?? 'Edu HSI', 0, 2) }}
             @endif
        </div>
        <h1 class="text-2xl font-bold text-gray-900">{{ $appSettings->app_name ?? 'Edu HSI' }}</h1>
        <p class="text-gray-500 text-sm">{{ $appSettings->app_slogan ?? 'Belajar Kapanpun, Dimanapun' }}</p>
    </div>

    <!-- Login Form -->
    <div class="bg-white p-8 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.05)] w-full max-w-md border border-gray-100" id="login-form-container">
        <h2 class="text-xl font-bold mb-6 text-center text-gray-800">{{ $appSettings->login_header_text ?? 'Masuk Akun' }}</h2>
        
        <div id="login-error" class="hidden bg-red-50 text-red-600 p-3 rounded-lg text-xs mb-4"></div>

        @if($errors->any() || session('error'))
            <div class="bg-red-50 text-red-600 p-3 rounded-lg text-xs mb-4">
                {{ $errors->first() ?? session('error') }}
            </div>
        @endif

        <form id="login-form" method="POST" action="/login" class="space-y-5">
            @csrf
            <div>
                <label class="text-xs font-bold text-gray-700 block mb-2">Email / NIP</label>
                <input type="text" name="email" placeholder="user@gmail.com" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:border-blue-900 focus:ring-0 outline-none bg-gray-50 placeholder-gray-400" required value="{{ old('email') }}">
            </div>
            <div>
                <label class="text-xs font-bold text-gray-700 block mb-2">Password</label>
                <input type="password" name="password" placeholder="••••••••" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:border-blue-900 focus:ring-0 outline-none bg-gray-50 placeholder-gray-400" required>
            </div>
            
            <button type="submit" id="login-submit-btn" class="w-full bg-blue-900 text-white font-bold py-3.5 rounded-xl hover:bg-blue-800 shadow-lg shadow-blue-900/20 transition-all active:scale-95">Masuk Sekarang</button>
        </form>

        <div class="mt-8 text-center space-y-2">
            <p class="text-xs text-gray-400">Belum punya akun?</p>
            <a href="/register" class="text-blue-900 font-bold text-sm hover:underline">Daftar Akun Baru</a>
        </div>
    </div>

    <!-- OTP Form (hidden by default) -->
    <div class="hidden bg-white p-8 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.05)] w-full max-w-md border border-gray-100" id="otp-form-container">
        <div class="text-center mb-4">
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2 text-blue-600">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                    <path fill-rule="evenodd" d="M12.516 2.17a.75.75 0 00-1.032 0 11.209 11.209 0 01-7.877 3.08.75.75 0 00-.722.515A12.74 12.74 0 002.25 9.75c0 5.942 4.064 10.933 9.563 12.348a.749.749 0 00.374 0c5.499-1.415 9.563-6.406 9.563-12.348 0-1.39-.223-2.73-.635-3.985a.75.75 0 00-.722-.516l-.143.001c-2.996 0-5.717-1.17-7.734-3.08zm3.094 8.016a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" />
                </svg>
            </div>
            <h3 class="font-bold text-lg">Verifikasi OTP</h3>
            <p class="text-xs text-gray-500 mt-1">Masukkan 6 digit kode yang dikirim ke <span id="otp-email-display" class="font-bold text-gray-700"></span></p>
        </div>
        
        <div id="otp-error" class="hidden bg-red-50 text-red-600 p-3 rounded-lg text-xs mb-4"></div>
        <div id="otp-success" class="hidden bg-green-50 text-green-600 p-3 rounded-lg text-xs mb-4"></div>

        <form id="otp-form" class="space-y-4">
            <input type="hidden" id="otp-email" name="email">
            <input type="hidden" id="otp-action" name="action" value="login">
            <div>
                <input type="text" id="otp-code" name="otp" required maxlength="6" placeholder="000000"
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-4 text-center text-2xl font-bold tracking-widest focus:outline-none focus:border-blue-500 focus:bg-white transition-colors">
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
        <button onclick="cancelOTP()" class="w-full text-xs text-gray-400 hover:text-gray-600 mt-4">← Kembali ke Login</button>
    </div>
</div>

<script>
    // Show OTP form
    function showOTPForm(email) {
        document.getElementById('login-form-container').classList.add('hidden');
        document.getElementById('otp-form-container').classList.remove('hidden');
        document.getElementById('otp-email').value = email;
        document.getElementById('otp-email-display').innerText = email;
        document.getElementById('otp-error').classList.add('hidden');
        document.getElementById('otp-success').classList.add('hidden');
        document.getElementById('otp-code').value = '';
        document.getElementById('otp-code').focus();
    }

    // Cancel OTP → back to login
    function cancelOTP() {
        document.getElementById('otp-form-container').classList.add('hidden');
        document.getElementById('login-form-container').classList.remove('hidden');
    }

    // Handle Login Form via AJAX
    document.getElementById('login-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('login-submit-btn');
        const originalText = btn.innerText;
        btn.innerText = 'Memproses...';
        btn.disabled = true;

        const errorEl = document.getElementById('login-error');
        errorEl.classList.add('hidden');

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
                showOTPForm(data.email);
            } else if (data.success) {
                window.location.href = data.redirect || '/?tab=profil';
            } else {
                errorEl.innerText = data.message || 'Login gagal.';
                errorEl.classList.remove('hidden');
            }
        })
        .catch(err => {
            console.error(err);
            errorEl.innerText = 'Terjadi kesalahan pada server.';
            errorEl.classList.remove('hidden');
        })
        .finally(() => {
            btn.innerText = originalText;
            btn.disabled = false;
        });
    });

    // Handle OTP Form via AJAX
    document.getElementById('otp-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('otp-submit-btn');
        const originalText = btn.innerText;
        btn.innerText = 'Memverifikasi...';
        btn.disabled = true;

        const formData = new FormData(this);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
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
                window.location.href = data.redirect || '/?tab=profil';
            } else {
                const errorEl = document.getElementById('otp-error');
                errorEl.innerText = data.message;
                errorEl.classList.remove('hidden');
            }
        })
        .catch(err => {
            console.error(err);
            document.getElementById('otp-error').innerText = 'Terjadi kesalahan pada verifikasi.';
            document.getElementById('otp-error').classList.remove('hidden');
        })
        .finally(() => {
            btn.innerText = originalText;
            btn.disabled = false;
        });
    });

    // Resend OTP
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
            const el = document.getElementById('otp-success');
            el.innerText = data.message;
            el.classList.remove('hidden');
            setTimeout(() => el.classList.add('hidden'), 5000);
        })
        .catch(err => {
            console.error(err);
            const el = document.getElementById('otp-error');
            el.innerText = 'Gagal mengirim ulang OTP.';
            el.classList.remove('hidden');
        })
        .finally(() => {
            btn.innerText = originalText;
            btn.disabled = false;
        });
    }
</script>
</body>
</html>
