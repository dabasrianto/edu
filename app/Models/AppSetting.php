<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    protected $fillable = [
        'key',
        'logo_path',
        'favicon_path',
        'theme_color',
        'font_family',
        'slider_config',
        'home_config',
        'menu_config',
        'blog_title',
        'blog_config',
        'academy_slogan',
        'academy_title',
        'regular_title',
        'regular_slogan',
        'payment_config',
        'google_login_enabled',
        'google_client_id',
        'google_client_secret',
        'login_header_text',
        'app_name',
        'app_slogan',
    ];

    protected $casts = [
        'slider_config' => 'array',
        'home_config' => 'array',
        'menu_config' => 'array',
        'blog_config' => 'array',
        'payment_config' => 'array',
        'google_login_enabled' => 'boolean',
    ];
}
