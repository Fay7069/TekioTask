<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'report_id';

    protected $fillable = [
        'generated_by',
        'student_id',
        'report_type',
        'file_url',
    ];

    protected $casts = [
        'generated_date' => 'datetime',
    ];

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by', 'user_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id', 'user_id');
    }
}
