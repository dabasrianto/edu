<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = ['course_id', 'title', 'duration', 'type', 'link', 'order', 'media_url', 'timer_seconds'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
