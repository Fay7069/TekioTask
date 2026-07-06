<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaseNote extends Model
{
    protected $primaryKey = 'note_id';

    protected $fillable = [
        'therapist_id',
        'student_id',
        'content',
    ];

    public function therapist()
    {
        return $this->belongsTo(User::class, 'therapist_id', 'user_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id', 'user_id');
    }
}
