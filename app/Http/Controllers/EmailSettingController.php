<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AppSetting;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class EmailSettingController extends Controller
{
    /**
     * Save email/SMTP configuration from admin dashboard.
     */
    public function update(Request $request)
    {
        $request->validate([
            'mail_mailer'       => 'required|string|in:smtp,sendmail,log',
            'mail_host'         => 'nullable|string|max:255',
            'mail_port'         => 'nullable|integer|min:1|max:65535',
            'mail_username'     => 'nullable|string|max:255',
            'mail_password'     => 'nullable|string|max:255',
            'mail_encryption'   => 'nullable|string|in:tls,ssl,null',
            'mail_from_address' => 'nullable|email|max:255',
            'mail_from_name'    => 'nullable|string|max:255',
        ]);

        $settings = AppSetting::firstOrCreate(['key' => 'main_settings']);

        $emailConfig = [
            'mail_mailer'       => $request->mail_mailer,
            'mail_host'         => $request->mail_host,
            'mail_port'         => (int) $request->mail_port,
            'mail_username'     => $request->mail_username,
            'mail_encryption'   => $request->mail_encryption === 'null' ? null : $request->mail_encryption,
            'mail_from_address' => $request->mail_from_address,
            'mail_from_name'    => $request->mail_from_name,
        ];

        // Only update password if provided (don't overwrite with empty)
        if ($request->filled('mail_password')) {
            $emailConfig['mail_password'] = encrypt($request->mail_password);
        } else {
            // Keep existing password
            $existing = $settings->email_config ?? [];
            $emailConfig['mail_password'] = $existing['mail_password'] ?? null;
        }

        $settings->email_config = $emailConfig;
        $settings->save();

        // Clear cache so new settings take effect
        \Illuminate\Support\Facades\Cache::forget('app_settings');

        return redirect()->back()->with('success', 'Konfigurasi email berhasil disimpan.');
    }

    /**
     * Send a test email to verify SMTP settings.
     */
    public function sendTestEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        // Apply dynamic mail config
        self::applyDatabaseMailConfig();

        try {
            Mail::raw('Ini adalah email percobaan dari ' . config('app.name') . '. Jika Anda menerima ini, berarti konfigurasi SMTP sudah benar! ✅', function ($message) use ($request) {
                $message->to($request->test_email)
                        ->subject('Test Email - ' . config('app.name'));
            });

            return response()->json([
                'success' => true,
                'message' => 'Email test berhasil dikirim ke ' . $request->test_email,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim email: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Apply mail configuration from database (AppSetting->email_config).
     * Call this before sending any email to use the admin-configured SMTP settings.
     */
    public static function applyDatabaseMailConfig()
    {
        $settings = AppSetting::firstOrCreate(['key' => 'main_settings']);
        $emailConfig = $settings->email_config ?? null;

        if (!$emailConfig || empty($emailConfig['mail_host'])) {
            return; // Fallback to .env defaults
        }

        Config::set('mail.default', $emailConfig['mail_mailer'] ?? 'smtp');
        Config::set('mail.mailers.smtp.host', $emailConfig['mail_host']);
        Config::set('mail.mailers.smtp.port', $emailConfig['mail_port'] ?? 587);
        Config::set('mail.mailers.smtp.encryption', $emailConfig['mail_encryption'] ?? 'tls');

        if (!empty($emailConfig['mail_username'])) {
            Config::set('mail.mailers.smtp.username', $emailConfig['mail_username']);
        }

        if (!empty($emailConfig['mail_password'])) {
            try {
                Config::set('mail.mailers.smtp.password', decrypt($emailConfig['mail_password']));
            } catch (\Exception $e) {
                // If decryption fails, use as-is (legacy)
                Config::set('mail.mailers.smtp.password', $emailConfig['mail_password']);
            }
        }

        if (!empty($emailConfig['mail_from_address'])) {
            Config::set('mail.from.address', $emailConfig['mail_from_address']);
        }

        if (!empty($emailConfig['mail_from_name'])) {
            Config::set('mail.from.name', $emailConfig['mail_from_name']);
        }

        // Purge the mailer so it picks up the new config
        Mail::purge('smtp');
    }
}
