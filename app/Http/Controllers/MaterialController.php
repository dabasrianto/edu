<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:150',
            'duration' => 'nullable|string|max:50',
            'type' => 'required|in:video,text,quiz',
            'link' => 'nullable|string|max:255',
            'media_url' => 'nullable|url|max:255',
            'timer_seconds' => 'nullable|integer|min:0',
        ]);

        Material::create($request->all());

        return back()->with('success', 'Materi berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        Material::findOrFail($id)->delete();
        return back()->with('success', 'Materi berhasil dihapus!');
    }
}
