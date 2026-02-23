<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $table = 'course_user'; // Map to Pivot Table

    protected $fillable = [
        'user_id',
        'course_id',
        'status',
        'payment_proof',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    
    // Define an accessor for easier status label if needed
}
