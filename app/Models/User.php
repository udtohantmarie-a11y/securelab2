<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passkeys\Contracts\PasskeyUser;
use Laravel\Passkeys\PasskeyAuthenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements PasskeyUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, PasskeyAuthenticatable;

    /**
     * Ang iyong database ay gumagamit ng 'user_id' sa halip na 'id'.
     * Kailangan itong i-specify para mahanap ng Laravel ang tamang record.
     */
    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     * In-update ito para tumugma sa column names ng iyong SQL dump.
     *
     * @var list<string>
     */
    protected $fillable = [
        'full_name',
        'email',
        'contact_number',
        'birthdate',
        'address',
        'password',
        'role',
        'is_active',
        'profile_photo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     * Itinatago ang sensitibong data gaya ng auth_code para sa pinto.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'auth_code',
        'verification_code',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'birthdate' => 'date',
        ];
    }

    /**
     * Helper method para malaman kung ang user ay Admin o Dean.
     * Mahalaga ito para sa mga intrusion alerts at system settings.
     */
    public function isPrivileged()
    {
        return in_array($this->role, ['Dean', 'Admin']);
    }

    /**
     * OVERRIDE: Passkeys Relationship
     * Because our primary key is 'user_id', Laravel incorrectly guesses 
     * the foreign key as 'user_user_id'. We explicitly set it to 'user_id' here.
     * Added ': HasMany' to satisfy the PasskeyUser interface requirement.
     */
    public function passkeys(): HasMany
    {
        return $this->hasMany(\Laravel\Passkeys\Passkey::class, 'user_id', 'user_id');
    }
}