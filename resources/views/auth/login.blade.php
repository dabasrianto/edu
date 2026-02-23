<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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

    <div class="bg-white p-8 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.05)] w-full max-w-md border border-gray-100">
        <h2 class="text-xl font-bold mb-6 text-center text-gray-800">{{ $appSettings->login_header_text ?? 'Masuk Akun' }}</h2>
        
        <form method="POST" action="/login" class="space-y-5">
            @csrf
            <div>
                <label class="text-xs font-bold text-gray-700 block mb-2">Email / NIP</label>
                <input type="text" name="email" placeholder="user@gmail.com" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:border-blue-900 focus:ring-0 outline-none bg-gray-50 placeholder-gray-400" required value="{{ old('email') }}">
                @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-bold text-gray-700 block mb-2">Password</label>
                <input type="password" name="password" placeholder="••••••••" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:border-blue-900 focus:ring-0 outline-none bg-gray-50 placeholder-gray-400" required>
                @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <button type="submit" class="w-full bg-blue-900 text-white font-bold py-3.5 rounded-xl hover:bg-blue-800 shadow-lg shadow-blue-900/20 transition-all active:scale-95">Masuk Sekarang</button>
            
            </div>

            <div class="mt-8 text-center space-y-2">
                <p class="text-xs text-gray-400">Belum punya akun?</p>
                <a href="/register" class="text-blue-900 font-bold text-sm hover:underline">Daftar Akun Baru</a>
            </div>
    </div>
</div>
</body>
</html>
