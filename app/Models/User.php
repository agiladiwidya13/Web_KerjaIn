<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'id', 'nama_lengkap', 'email', 'password', 'role', 'foto_profil',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // ── Relationships ────────────────────────────────────────

    public function pelajar()
    {
        return $this->hasOne(Pelajar::class, 'user_id');
    }

    public function mentor()
    {
        return $this->hasOne(Mentor::class, 'user_id');
    }

    public function mitra()
    {
        return $this->hasOne(Mitra::class, 'user_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'pelajar_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function badges()
    {
        return $this->hasMany(UserBadge::class, 'user_id');
    }

    // ── Helpers ──────────────────────────────────────────────

    /**
     * Mendapatkan profil sesuai role.
     */
    public function profile()
    {
        return match ($this->role) {
            'pelajar' => $this->pelajar,
            'mentor'  => $this->mentor,
            'mitra'   => $this->mitra,
            default   => null,
        };
    }
}
