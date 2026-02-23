<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Session Driver: " . config('session.driver') . "\n";
echo "Session Domain: " . config('session.domain') . "\n";
echo "Session Secure: " . (config('session.secure') ? 'true' : 'false') . "\n";
echo "Session SameSite: " . config('session.same_site') . "\n";
echo "Session Lifetime: " . config('session.lifetime') . "\n";
