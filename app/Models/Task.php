<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $primaryKey = 'task_id';

    public $timestamps = false;

    protected $fillable = [
        'routine_id',
        'title',
        'estimated_duration_seconds',
        'has_micro_steps',
        'display_order',
    ];

    protected $casts = [
        'has_micro_steps' => 'boolean',
    ];

    // ── Relationships ─────────────────────────────────────────


    public function routine()
    {
        return $this->belongsTo(Routine::class, 'routine_id', 'routine_id');
    }

    public function microSteps()
    {
        return $this->hasMany(MicroStep::class, 'task_id', 'task_id')
                    ->orderBy('step_order');
    }

    public function progressLogs()
    {
        return $this->hasMany(ProgressLog::class, 'task_id', 'task_id');
    }
}
