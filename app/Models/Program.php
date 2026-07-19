<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'id', 'mitra_id', 'judul', 'deskripsi', 'bidang', 'cover_image',
        'status', 'kuota', 'registrasi_mulai', 'registrasi_selesai',
        'tanggal_mulai', 'tanggal_selesai',
    ];

    protected $casts = [
        'registrasi_mulai'   => 'date',
        'registrasi_selesai' => 'date',
        'tanggal_mulai'      => 'date',
        'tanggal_selesai'    => 'date',
    ];

    // ── Relationships ────────────────────────────────────────

    public function mitra()
    {
        return $this->belongsTo(Mitra::class, 'mitra_id');
    }

    public function mentors()
    {
        return $this->belongsToMany(Mentor::class, 'program_mentors', 'program_id', 'mentor_id')
                    ->withPivot('status', 'applied_at', 'reviewed_at');
    }

    public function approvedMentors()
    {
        return $this->mentors()->wherePivot('status', 'disetujui');
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

    public function scopeRegistrationOpen($query)
    {
        return $query->where('registrasi_mulai', '<=', now())
                     ->where('registrasi_selesai', '>=', now());
    }

    // ── Helpers ──────────────────────────────────────────────

    public function isRegistrationOpen(): bool
    {
        if (!$this->registrasi_mulai || !$this->registrasi_selesai) {
            return false;
        }
        return now()->between($this->registrasi_mulai, $this->registrasi_selesai);
    }

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
