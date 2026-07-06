<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoutineAssignment extends Model
{
    protected $primaryKey = 'assignment_id';

    // The original migration has no created_at/updated_at columns
    public $timestamps = false;

    protected $fillable = [
        'routine_id',
        'student_id',
        'group_id',
        'assigned_date',
        'is_active',
    ];

    protected $casts = [
        'assigned_date' => 'date',
        'is_active'     => 'boolean',
    ];

    public function routine()
    {
        return $this->belongsTo(Routine::class, 'routine_id', 'routine_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id', 'user_id');
    }

    public function group()
    {
        return $this->belongsTo(StudentGroup::class, 'group_id', 'group_id');
    }
}
