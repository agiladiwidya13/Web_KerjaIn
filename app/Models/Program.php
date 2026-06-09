<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'id', 'mitra_id', 'judul', 'deskripsi', 'bidang', 'cover_image',
        'status', 'kuota', 'tanggal_mulai', 'tanggal_selesai',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
    ];

    // ── Relationships ────────────────────────────────────────

    public function mitra()
    {
        return $this->belongsTo(Mitra::class, 'mitra_id');
    }

    public function mentors()
    {
        return $this->belongsToMany(Mentor::class, 'program_mentors', 'program_id', 'mentor_id')
                    ->withPivot('assigned_at');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'program_id')->orderBy('urutan');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'program_id');
    }

    // ── Scopes ───────────────────────────────────────────────

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    // ── Helpers ──────────────────────────────────────────────

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function enrolledCount(): int
    {
        return $this->enrollments()->where('status', '!=', 'ditolak')->count();
    }

    public function isFull(): bool
    {
        if ($this->kuota === null) return false;
        return $this->enrolledCount() >= $this->kuota;
    }
}
