<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgressLog extends Model
{
    protected $primaryKey = 'log_id';

    public $timestamps = false;

    protected $fillable = [
        'student_id',
        'task_id',
        'status',
        'time_taken_seconds',
        'attempt_timestamp',
        'was_adapted',
    ];

    protected $casts = [
        'attempt_timestamp' => 'datetime',
        'was_adapted'       => 'boolean',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id', 'task_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id', 'user_id');
    }
}
