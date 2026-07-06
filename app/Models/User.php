<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'name',
        'email',
        'password_hash',
        'role',
        'age',
        'diagnosis',
        'accessibility_settings',
    ];

    protected $hidden = [];

    protected $casts = [
        'accessibility_settings' => 'array',
    ];

    // ── Required by Laravel's Auth system ────────────────────
    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    // ── Accessibility settings helper ─────────────────────────
    // Usage: $user->setting('large_buttons') ?? false
    public function setting(string $key, mixed $default = null): mixed
    {
        $settings = $this->accessibility_settings ?? [];

        if (is_string($settings)) {
            $settings = json_decode($settings, true) ?? [];
        }

        return $settings[$key] ?? $default;
    }

    // ── Relationships ─────────────────────────────────────────

    public function routines()
    {
        return $this->hasMany(Routine::class, 'teacher_id', 'user_id');
    }

    public function progressLogs()
    {
        return $this->hasMany(ProgressLog::class, 'student_id', 'user_id');
    }

    public function reward()
    {
        return $this->hasOne(Reward::class, 'student_id', 'user_id');
    }

    public function parentComments()
    {
        return $this->hasMany(ParentComment::class, 'student_id', 'user_id');
    }

    public function homeProgress()
    {
        return $this->hasMany(HomeProgress::class, 'student_id', 'user_id');
    }

    public function caseNotes()
    {
        return $this->hasMany(CaseNote::class, 'student_id', 'user_id');
    }

    public function notifications()
    {
        return $this->hasMany(TekioNotification::class, 'user_id', 'user_id');
    }

    // Groups this student belongs to (via group_members pivot)
    public function groups()
    {
        return $this->belongsToMany(
            StudentGroup::class,
            'group_members',
            'student_id',
            'group_id',
            'user_id',
            'group_id'
        );
    }
}
