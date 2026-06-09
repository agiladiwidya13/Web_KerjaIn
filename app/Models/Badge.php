<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // hanya created_at

    protected $fillable = [
        'id', 'nama', 'deskripsi', 'icon_url', 'syarat', 'created_at'
    ];
}
