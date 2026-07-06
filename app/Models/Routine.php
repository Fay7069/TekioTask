<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Routine extends Model
{
    protected $primaryKey = 'routine_id';

    protected $fillable = ['name', 'teacher_id'];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id', 'user_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'routine_id', 'routine_id')
                    ->orderBy('display_order');
    }

    public function assignments()
    {
        return $this->hasMany(RoutineAssignment::class, 'routine_id', 'routine_id');
    }
}
