<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $fillable = [
        'title',
        'description',
        'category',
        'color',
        'type', // wajib, sunnah
        'duration_minutes',
        'start_time',
        'end_time',
        'is_active',
        'show_result',
        'certificate_threshold',
        'certificate_template_id',
    ];

    public function certificateTemplate()
    {
        return $this->belongsTo(CertificateTemplate::class);
    }

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_active' => 'boolean',
        'show_result' => 'boolean',
    ];

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('order');
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }
}
