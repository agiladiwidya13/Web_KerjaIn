<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mentor extends Model
{
    public $incrementing = false;
    protected $keyType   = 'string';
    protected $table     = 'mentor';

    protected $fillable  = ['id', 'user_id', 'profesi', 'perusahaan', 'tahun_pengalaman', 'bio_keahlian'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
