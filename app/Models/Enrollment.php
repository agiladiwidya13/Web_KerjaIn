<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'id', 'pelajar_id', 'program_id', 'status', 'enrolled_at', 'selesai_at',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'selesai_at'  => 'datetime',
    ];

    public function pelajar()
    {
        return $this->belongsTo(User::class, 'pelajar_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'enrollment_id');
    }

    public function portfolio()
    {
        return $this->hasOne(Portfolio::class, 'enrollment_id');
    }

    public function certificate()
    {
        return $this->hasOne(Certificate::class, 'enrollment_id');
    }

    // ── Helpers ──────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status === 'aktif';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'selesai';
    }

    /**
     * Hitung progress: jumlah submission disetujui / total task.
     */
    public function progressPercent(): int
    {
        $totalTasks = $this->program->tasks()->count();
        if ($totalTasks === 0) return 0;

        $approved = $this->submissions()->where('status', 'disetujui')->count();
        return (int) round(($approved / $totalTasks) * 100);
    }
}
