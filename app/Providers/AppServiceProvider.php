<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\View;
use App\Models\AppSetting;
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
        // Fix standard key length for older MySQL if needed (optional)
        Builder::defaultStringLength(191);

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
