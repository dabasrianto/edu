<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiSetting extends Model
{
    protected $fillable = [
        'provider',
        'api_key',
        'models',
        'selected_model',
        'system_prompt',
        'reference_url',
        'is_active',
        'is_widget_active',
    ];

    protected $casts = [
        'models' => 'array',
        'is_active' => 'boolean',
        'is_widget_active' => 'boolean',
    ];
}
