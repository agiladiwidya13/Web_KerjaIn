<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelajar extends Model
{
    public $incrementing = false;
    protected $keyType   = 'string';
    protected $table     = 'pelajar';

    protected $fillable = ['id', 'user_id', 'universitas', 'jurusan', 'angkatan', 'bio', 'total_poin'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'pelajar_id', 'user_id');
    }

    public function portfolios()
    {
        return $this->hasMany(Portfolio::class, 'pelajar_id', 'user_id');
    }

    public function certificates()
    {
        return $this->hasManyThrough(Certificate::class, Enrollment::class, 'pelajar_id', 'enrollment_id', 'user_id', 'id');
    }
}
