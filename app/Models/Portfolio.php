<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'id', 'enrollment_id', 'pelajar_id', 'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class, 'enrollment_id');
    }

    public function pelajar()
    {
        return $this->belongsTo(User::class, 'pelajar_id');
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }
}
