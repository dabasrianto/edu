<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'image',
        'rating',
        'sold_count',
        'link',
        'is_active',
    ];
}
