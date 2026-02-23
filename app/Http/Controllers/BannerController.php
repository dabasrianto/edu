<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    /**
     * Display the specified resource (Public View).
     */
    public function show($slug)
    {
        $banner = Banner::where('slug', $slug)->where('is_active', true)->firstOrFail();
        return view('post', compact('banner'));
    }

    /**
     * Store a newly created resource in storage (Admin).
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|max:2048', // 2MB Max
            'subtitle' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'order' => 'nullable|integer',
        ]);

        $path = $request->file('image')->store('banners', 'public');

        Banner::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . Str::random(5),
            'subtitle' => $request->subtitle,
            'content' => $request->content,
            'image' => $path,
            'is_active' => true,
            'order' => $request->input('order', 1),
        ]);

        // If we want to persist the "Admin" tab, we should probably redirect back
        return redirect()->back()->with('success', 'Banner berhasil ditambahkan!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
         // TODO: Implement update if needed, for now we might only have create/delete as per MVP
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);
        
        // Delete image
        if ($banner->image) {
            Storage::disk('public')->delete($banner->image);
        }

        $banner->delete();

        return redirect()->back()->with('success', 'Banner berhasil dihapus!');
    }
}
