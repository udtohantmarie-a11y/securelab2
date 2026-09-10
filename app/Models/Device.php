<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $table = 'devices';
    protected $primaryKey = 'device_id';

    protected $fillable = [
        'room_id', 'device_code', 'mac_address', 'is_online', 'last_ping_at'
    ];

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }
}