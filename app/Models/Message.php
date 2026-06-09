<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // Karena hanya ada created_at dan read_at di tabel

    protected $fillable = [
        'id', 'sender_id', 'receiver_id', 'isi', 'read_at', 'created_at'
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
