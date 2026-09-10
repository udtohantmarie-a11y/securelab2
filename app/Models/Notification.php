<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'notif_id';
    public $timestamps = false; 

    protected $fillable = [
        'user_id', 
        'room_id', 
        'type', 
        'title', 
        'body', 
        'is_read', 
        'reference_id', 
        'reference_table'
    ];

    // Relationship para sa recipient ng notification
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // Relationship para malaman kung anong kwarto ang may alert
    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'room_id');
    }
}