<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FailureTracker extends Model
{
    protected $table      = 'failure_tracker';
    protected $primaryKey = 'failure_id';

    public $timestamps = false;

    protected $fillable = [
        'student_id',
        'task_id',
        'consecutive_failures',
        'last_failure_date',
    ];

    protected $casts = [
        'last_failure_date' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id', 'user_id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id', 'task_id');
    }
}
