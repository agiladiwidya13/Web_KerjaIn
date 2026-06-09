<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoinLog extends Model
{
    protected $table = 'poin_log';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // Karena tabel ini mungkin hanya punya created_at (sesuai kerjain_db_v2.sql)

    protected $fillable = [
        'id', 'pelajar_id', 'jumlah', 'keterangan', 'referensi_type', 'referensi_id', 'created_at'
    ];

    public function pelajar()
    {
        return $this->belongsTo(User::class, 'pelajar_id'); // Mengarah ke user(pelajar)
    }
}
