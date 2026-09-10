<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntrusionAlert extends Model
{
    protected $table = 'intrusion_alerts';
    protected $primaryKey = 'alert_id';
    public $timestamps = false; // Dahil 'triggered_at' ang gamit sa SQL

    protected $fillable = [
        'room_id', 'device_id', 'alert_type', 'severity', 'description', 'is_resolved'
    ];
}