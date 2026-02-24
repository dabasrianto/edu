<x-mail::message>
# Kode Verifikasi OTP

Gunakan kode di bawah ini untuk memverifikasi akun Anda. Kode ini akan kedaluwarsa dalam 10 menit.

<x-mail::panel>
# {{ $otp }}
</x-mail::panel>

Jika Anda tidak merasa melakukan permintaan ini, abaikan saja email ini.

Terima kasih,<br>
{{ config('app.name') }}
</x-mail::message>
