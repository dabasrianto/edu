<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'subtitle',
        'content',
        'image',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : 'https://placehold.co/800x400?text=Banner';
    }
}
