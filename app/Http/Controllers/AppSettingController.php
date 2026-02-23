<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AppSettingController extends Controller
{
    public function update(Request $request)
    {
        $settings = \App\Models\AppSetting::firstOrCreate([], ['id' => 1]);
        
        $data = $request->validate([
            'logo' => 'nullable|image|max:2048',
            'favicon' => 'nullable|image|max:1024',
            'font_family' => 'required|string',
            'theme_color' => 'required|string',
            'home_greeting' => 'nullable|string',
            'wp_sync_enabled' => 'nullable|boolean',
            'wp_sync_url' => 'nullable|url',
            'wp_sync_category_id' => 'nullable|exists:categories,id',
            'wp_sync_limit' => 'nullable|integer|min:1|max:100',
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('settings', 'public');
            $settings->logo_path = $path;
        }

        if ($request->hasFile('favicon')) {
            $path = $request->file('favicon')->store('settings', 'public');
            $settings->favicon_path = $path;
        }

        $settings->font_family = $data['font_family'];
        $settings->theme_color = $data['theme_color'];
        
        $homeConfig = $settings->home_config ?? [];
        $homeConfig['greeting'] = $data['home_greeting'];
        $settings->home_config = $homeConfig;

        $blogConfig = $settings->blog_config ?? [];
        $blogConfig['wp_sync_enabled'] = $request->has('wp_sync_enabled');
        $blogConfig['wp_sync_url'] = $data['wp_sync_url'] ?? null;
        $blogConfig['wp_sync_category_id'] = $data['wp_sync_category_id'] ?? null;
        $blogConfig['wp_sync_limit'] = $data['wp_sync_limit'] ?? 10;
        $settings->blog_config = $blogConfig;

        $settings->save();

        return redirect()->back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
