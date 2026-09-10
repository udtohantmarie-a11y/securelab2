<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    /**
     * Ang pangalan ng table sa iyong database.
     * Base sa SQL dump mo, ito ay 'audit_logs'.
     */
    protected $table = 'audit_logs';

    /**
     * Ang primary key ng table.
     */
    protected $primaryKey = 'log_id';

    /**
     * Dahil ang SQL dump mo ay may 'logged_at' sa halip na 'created_at' at 'updated_at',
     * i-disable natin ang default timestamps ng Laravel.
     */
    public $timestamps = false;

    /**
     * Ang mga attributes na pwedeng i-save (Mass Assignable).
     */
    protected $fillable = [
        'room_id',
        'device_id',
        'user_id',
        'action',
        'method',
        'door_state_after',
        'ip_address',
        'notes',
        'logged_at'
    ];

    /**
     * Relationship: Ang log ay pag-aari ng isang User.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Relationship: Ang log ay para sa isang Room.
     */
    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'room_id');
    }
}