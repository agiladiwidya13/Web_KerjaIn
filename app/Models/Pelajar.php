<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelajar extends Model
{
    public $incrementing = false;
    protected $keyType   = 'string';
    protected $table     = 'pelajar';

    protected $fillable  = ['id', 'user_id', 'universitas', 'jurusan', 'angkatan', 'bio'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
