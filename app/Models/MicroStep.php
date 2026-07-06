<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MicroStep extends Model
{
    protected $table      = 'micro_steps';
    protected $primaryKey = 'step_id';
    public    $timestamps = false;

    protected $fillable = [
        'task_id',
        'step_order',
        'description',  // actual column name in DB
        'image_url',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id', 'task_id');
    }
}
