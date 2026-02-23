<?php
// scripts/check_settings.php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$settings = \App\Models\AppSetting::first();
if ($settings) {
    echo "Login Header: " . $settings->login_header_text . "\n";
    echo "App Name: " . $settings->app_name . "\n";
    echo "App Slogan: " . $settings->app_slogan . "\n";
} else {
    echo "No settings found.\n";
}
