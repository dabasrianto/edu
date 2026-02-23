<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'type',
        'content',
        'image',
        'category_id',
        'status',
        'order',
        'show_in_slider',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title . '-' . Str::random(5));
            }
        });
    }

    // Accessor for image URL
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }
    
    // Relationship stub (if needed)
    public function category() {
        return $this->belongsTo(Category::class)->withDefault(['name' => 'Umum']);
    }
}


