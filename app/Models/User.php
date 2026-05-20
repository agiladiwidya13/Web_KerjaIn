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
        'id', 'nama_lengkap', 'email', 'password_hash', 'role', 'foto_profil',
    ];

    protected $hidden = ['password_hash'];

    // Eloquent pakai password_hash bukan password
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

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
}
