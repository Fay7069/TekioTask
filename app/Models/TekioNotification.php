<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Named TekioNotification to avoid conflict with Laravel's built in Notification class.
// The actual DB table is still called 'notifications'.

class TekioNotification extends Model
{
    public $timestamps = false;

    protected $table = 'notifications';

    protected $primaryKey = 'notification_id';

    protected $fillable = [
        'user_id',
        'type',
        'message',
        'is_read',
        'sent_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'sent_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
