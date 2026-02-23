<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminSettingsController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png,jpg,jpeg|max:1024',
            'theme_color' => 'required|string',
            'font_family' => 'required|string',
        ]);

        $settings = AppSetting::firstOrCreate(['key' => 'main_settings']);

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($settings->logo_path) {
                Storage::disk('public')->delete($settings->logo_path);
            }
            // Store new logo
            $path = $request->file('logo')->store('logos', 'public');
            $settings->logo_path = $path;
        }

        if ($request->hasFile('favicon')) {
            // Delete old favicon if exists
            if ($settings->favicon_path) {
                Storage::disk('public')->delete($settings->favicon_path);
            }
            // Store new favicon
            $path = $request->file('favicon')->store('favicons', 'public');
            $settings->favicon_path = $path;
        }

        $settings->theme_color = $request->theme_color;
        $settings->font_family = $request->font_family;
        
        // Handle Home & Menu Configs
        if ($request->has('home_config')) {
            $settings->home_config = $request->home_config;
        }
        if ($request->has('menu_config')) {
            $settings->menu_config = $request->menu_config;
        }
        
        $settings->save();

        return back()->with('success', 'Pengaturan tema berhasil diperbarui!');
    }
    public function uploadSlide(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048',
            'title' => 'nullable|string|max:50',
            'subtitle' => 'nullable|string|max:100',
        ]);

        $settings = AppSetting::firstOrCreate(['key' => 'main_settings']);
        
        $path = $request->file('image')->store('sliders', 'public');
        
        $slides = $settings->slider_config ?? [];
        $slides[] = [
            'image' => $path,
            'title' => $request->title,
            'subtitle' => $request->subtitle,
        ];
        
        $settings->slider_config = $slides;
        $settings->save();

        return back()->with('success', 'Slide berhasil ditambahkan!');
    }

    public function deleteSlide(Request $request, $index)
    {
        $settings = AppSetting::firstOrCreate(['key' => 'main_settings']);
        $slides = $settings->slider_config ?? [];
        
        if (isset($slides[$index])) {
            // Delete file
            if (Storage::disk('public')->exists($slides[$index]['image'])) {
                Storage::disk('public')->delete($slides[$index]['image']);
            }
            // Remove from array
            unset($slides[$index]);
            // Re-index array
            $settings->slider_config = array_values($slides);
            $settings->save();
        }

        return back()->with('success', 'Slide berhasil dihapus!');
    }
}
