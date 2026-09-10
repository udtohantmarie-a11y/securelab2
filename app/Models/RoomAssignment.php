<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomAssignment extends Model
{
    protected $table = 'room_assignments';
    protected $primaryKey = 'assignment_id';
    public $timestamps = false; // Dahil 'assigned_at' lang ang nasa SQL mo

    protected $fillable = [
        'user_id', 'room_id', 'assigned_by', 'access_level', 'is_active', 'notes'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }
}