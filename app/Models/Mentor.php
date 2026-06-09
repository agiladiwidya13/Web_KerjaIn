<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mentor extends Model
{
    public $incrementing = false;
    protected $keyType   = 'string';
    protected $table     = 'mentor';

    protected $fillable = [
        'id', 'user_id', 'mitra_id', 'profesi', 'perusahaan',
        'tahun_pengalaman', 'bio_keahlian',
    ];

    // ── Relationships ────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * [*] Afiliasi ke Mitra perusahaan.
     */
    public function mitra()
    {
        return $this->belongsTo(Mitra::class, 'mitra_id');
    }

    /**
     * Program yang di-assign ke mentor ini.
     */
    public function programs()
    {
        return $this->belongsToMany(Program::class, 'program_mentors', 'mentor_id', 'program_id')
                    ->withPivot('assigned_at');
    }

    /**
     * Submission yang di-review oleh mentor ini.
     */
    public function reviewedSubmissions()
    {
        return $this->hasMany(Submission::class, 'reviewed_by');
    }

    // ── Scopes ───────────────────────────────────────────────

    /**
     * Scope: hanya mentor yang terafiliasi dengan mitra tertentu.
     */
    public function scopeBelongsToCompany($query, $mitraId)
    {
        return $query->where('mitra_id', $mitraId);
    }
}
