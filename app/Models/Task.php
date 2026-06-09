<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'id', 'program_id', 'judul', 'deskripsi', 'deadline', 'urutan',
    ];

    protected $casts = [
        'deadline' => 'datetime',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'task_id');
    }
}
