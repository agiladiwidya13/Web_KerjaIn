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
        return $this->belongsTo(Pelajar::class, 'pelajar_id', 'user_id');
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

    /**
     * Return human readable duration between enrolled_at and selesai_at.
     * If the duration is more than 6 days, represent it in weeks + days.
     * Inclusive of start and end date (same-day = 1 Hari).
     */
    public function durationHuman(): string
    {
        if (! $this->enrolled_at || ! $this->selesai_at) return '-';

        $days = $this->enrolled_at->diffInDays($this->selesai_at) + 1;

        if ($days <= 6) {
            return $days . ' Hari';
        }

        $weeks = intdiv($days, 7);
        $remainder = $days % 7;

        $parts = [];
        if ($weeks > 0) $parts[] = $weeks . ' Minggu';
        if ($remainder > 0) $parts[] = $remainder . ' Hari';

        return implode(' ', $parts);
    }
}
