<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParentComment extends Model
{
    public $timestamps = false;

    const CREATED_AT = 'created_at';

    protected $primaryKey = 'comment_id';

    protected $fillable = [
        'parent_id',
        'student_id',
        'comment_text',
    ];

    protected $casts = [
        'created_at' => 'datetime',
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
