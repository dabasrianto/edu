<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ManifestController extends Controller
{
    public function index()
    {
        $settings = AppSetting::first();
        
        $appName = $settings->app_name ?? 'HSI Edu';
        $shortName = $settings->app_name ?? 'EduHSI'; // Fallback to app_name if no short_name logic
        // Try to derive short name if possible or just use app name
        // For now, let's use the app name for both if custom logic isn't defined.
        
        $themeColor = $settings->theme_color ?? '#1e3a8a';
        // Map theme color name to hex if necessary, or assume it's stored as hex/valid css color.
        // Based on AppSetting model, theme_color is a select option (blue, red, etc.)
        // We need a mapping for hex codes for manifest
        
        $colorMap = [
            'blue' => '#1e3a8a',
            'red' => '#b91c1c',
            'emerald' => '#047857',
            'purple' => '#6d28d9',
            'gray' => '#374151',
            'orange' => '#c2410c',
            'amber' => '#b45309',
            'teal' => '#0f766e',
            'cyan' => '#0e7490',
            'indigo' => '#4338ca',
            'rose' => '#be123c',
            'pink' => '#be185d',
        ];
        
        $hexColor = $colorMap[$themeColor] ?? '#1e3a8a';

        $iconSrc = $settings->logo_path 
            ? Storage::url($settings->logo_path) 
            : "https://ui-avatars.com/api/?name=" . urlencode($appName) . "&background=" . str_replace('#', '', $hexColor) . "&color=fff&size=192";
            
        // For larger icon
        $iconSrcLarge = $settings->logo_path 
            ? Storage::url($settings->logo_path) 
            : "https://ui-avatars.com/api/?name=" . urlencode($appName) . "&background=" . str_replace('#', '', $hexColor) . "&color=fff&size=512";

        return response()->json([
            "name" => $appName,
            "short_name" => substr($appName, 0, 12), // simple truncation for short_name
            "start_url" => "/",
            "display" => "standalone",
            "background_color" => $hexColor,
            "theme_color" => $hexColor,
            "description" => $settings->app_slogan ?? "Platform Belajar HSI",
            "icons" => [
                [
                    "src" => $iconSrc,
                    "sizes" => "192x192",
                    "type" => "image/png"
                ],
                [
                    "src" => $iconSrcLarge,
                    "sizes" => "512x512",
                    "type" => "image/png"
                ]
            ]
        ]);
    }
}
