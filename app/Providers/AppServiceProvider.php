<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\View;
use App\Models\AppSetting;
use App\Models\AiSetting;
use Illuminate\Database\Schema\Builder; // Optional fix for key length

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Fix cURL SSL certificate path
        // Force Guzzle/Http Client to use the correct CA bundle if found
        if (file_exists('C:\laragon\etc\ssl\cacert.pem')) {
            // This works for Laravel Http Facade requests
            \Illuminate\Support\Facades\Http::globalOptions([
                'verify' => 'C:\laragon\etc\ssl\cacert.pem',
            ]);
            
            // Try to set environment variable for other libs
            putenv("CURL_CA_BUNDLE=C:\laragon\etc\ssl\cacert.pem");
            putenv("SSL_CERT_FILE=C:\laragon\etc\ssl\cacert.pem");
            putenv("KB_CA_CERTS=C:\laragon\etc\ssl\cacert.pem");
        }

        // Fix standard key length for older MySQL if needed (optional)
        Builder::defaultStringLength(191);

        // Check for AI Chat Feature
        try {
            View::composer('*', function ($view) {
                try {
                    // Check if table exists to avoid migration errors
                    if (!\Illuminate\Support\Facades\Schema::hasTable('ai_settings')) {
                        $view->with('aiChatEnabled', false);
                        return;
                    }

                    $aiEnabled = \App\Models\AiSetting::where('is_active', true)
                        ->where('is_widget_active', true)
                        ->exists();
                    $view->with('aiChatEnabled', $aiEnabled);
                } catch (\Exception $e) {
                    // dump($e->getMessage());
                    $view->with('aiChatEnabled', false);
                }
            });
        } catch (\Exception $e) {
            // Failsafe
        }

        try {
            $settings = AppSetting::firstOrCreate(
                ['key' => 'main_settings'],
                ['theme_color' => 'blue', 'font_family' => 'Inter', 'slider_config' => []]
            );
            View::share('appSettings', $settings);
            
            // Dynamic Google Configuration
            if ($settings->google_client_id && $settings->google_client_secret) {
                config([
                    'services.google.client_id' => $settings->google_client_id,
                    'services.google.client_secret' => $settings->google_client_secret,
                    'services.google.redirect' => url('/auth/google/callback'),
                ]);
            }

            View::share('courses', \App\Models\Course::all());
            try {
                 View::share('bankAccounts', \App\Models\BankAccount::where('is_active', true)->get());
            } catch (\Exception $e) {}
        } catch (\Exception $e) {
            // Failsafe
        }
    }
}
