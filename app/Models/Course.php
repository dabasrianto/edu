<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'title', 'slug', 'short_desc', 'type', 'price', 'currency', 'color', 'teacher_id'
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function materials()
    {
        return $this->hasMany(Material::class)->orderBy('order');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'course_user')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}
