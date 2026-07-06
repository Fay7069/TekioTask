<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentGroup extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'group_id';

    protected $fillable = ['group_name', 'teacher_id'];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id', 'user_id');
    }

    public function members()
    {
        return $this->belongsToMany(
            User::class,
            'group_members',
            'group_id',
            'student_id',
            'group_id',
            'user_id'
        );
    }
}
