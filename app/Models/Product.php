<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'type',
        'description',
        'price',
        'image',
        'rating',
        'sold_count',
        'link',
        'file_path',
        'download_url',
        'is_active',
    ];
}
