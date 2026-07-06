<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'reward_id';

    protected $fillable = [
        'student_id',
        'points',
        'badges',
    ];

    protected $casts = [
        'badges' => 'array',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id', 'user_id');
    }
}
