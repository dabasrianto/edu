<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'whatsapp',
        'address',
        'role',
        'balance',
        'is_active',
        'nip',
        'avatar',
        'google_id',
        'avatar_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'balance' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return ($this->role === 'admin' || $this->role === 'ustad') && $this->is_active;
    }

    public function isUstad(): bool
    {
        return $this->role === 'ustad';
    }

    public function teachingCourses()
    {
        return $this->hasMany(Course::class, 'teacher_id');
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_user')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
