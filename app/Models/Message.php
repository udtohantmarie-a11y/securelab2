<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'messages';
    protected $primaryKey = 'message_id';
    
    // Ginamit ang timestamp(3) sa SQL kaya i-disable muna natin ang default Eloquent timestamps
    public $timestamps = false; 

    protected $fillable = [
        'convo_id', 
        'sender_id', 
        'body', 
        'is_read'
    ];

    // Relationship para malaman kung sino ang nag-send
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id', 'user_id');
    }
}