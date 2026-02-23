<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show($id)
    {
        $product = \App\Models\Product::where('is_active', true)->findOrFail($id);
        return view('product.show', compact('product'));
    }
}
