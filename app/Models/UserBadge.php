<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBadge extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // hanya earned_at

    protected $fillable = [
        'id', 'user_id', 'badge_id', 'earned_at'
    ];

    public function badge()
    {
        return $this->belongsTo(Badge::class, 'badge_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
