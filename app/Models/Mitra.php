<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mitra extends Model
{
    public $incrementing = false;
    protected $keyType   = 'string';
    protected $table     = 'mitra';

    protected $fillable  = ['id', 'user_id', 'nama_usaha', 'bidang_usaha', 'kota', 'kontak_bisnis'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
