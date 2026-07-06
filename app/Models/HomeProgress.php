<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeProgress extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'home_task_id';

    protected $fillable = [
        'parent_id',
        'student_id',
        'task_name',
        'completed_date',
    ];

    protected $casts = [
        'completed_date' => 'date',
        'recorded_at'    => 'datetime',
    ];

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id', 'user_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id', 'user_id');
    }
}
