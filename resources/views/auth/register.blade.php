<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100">
<div class="min-h-screen flex items-center justify-center">
    <div class="bg-white p-6 rounded-xl shadow-md w-full max-w-md">
        <h1 class="text-xl font-bold mb-4">Registrasi Pengguna</h1>
        <form method="POST" action="/register" class="space-y-4">
            @csrf
            <div>
                <label class="text-sm font-medium">Nama</label>
                <input type="text" name="name" class="mt-1 w-full border rounded-lg px-3 py-2" required value="{{ old('name') }}">
                @error('name')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-sm font-medium">Email</label>
                <input type="email" name="email" class="mt-1 w-full border rounded-lg px-3 py-2" required value="{{ old('email') }}">
                @error('email')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-sm font-medium">Password</label>
                <input type="password" name="password" class="mt-1 w-full border rounded-lg px-3 py-2" required>
                @error('password')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-sm font-medium">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="mt-1 w-full border rounded-lg px-3 py-2" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-2 rounded-lg">Daftar</button>
        </form>
        <p class="text-xs text-gray-500 mt-4">Ingin mendaftar admin? <a class="text-blue-600" href="/admin/register">Admin register</a></p>
    </div>
</div>
</body>
</html>
