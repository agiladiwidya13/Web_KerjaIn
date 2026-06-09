<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mitra extends Model
{
    public $incrementing = false;
    protected $keyType   = 'string';
    protected $table     = 'mitra';

    protected $fillable = [
        'id', 'user_id', 'nama_usaha', 'bidang_usaha', 'kota',
        'kontak_bisnis', 'email_domain', 'logo_perusahaan', 'website',
    ];

    // ── Relationships ────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * [*] Semua Mentor yang terafiliasi via email_domain.
     */
    public function mentors()
    {
        return $this->hasMany(Mentor::class, 'mitra_id');
    }

    /**
     * Program yang diterbitkan oleh Mitra ini.
     */
    public function programs()
    {
        return $this->hasMany(Program::class, 'mitra_id');
    }

    // ── Helpers ──────────────────────────────────────────────

    /**
     * Mengekstrak domain dari email.
     */
    public static function extractDomain(string $email): string
    {
        return strtolower(substr(strrchr($email, '@'), 1));
    }

    /**
     * Cari Mitra berdasarkan email domain.
     */
    public static function findByDomain(string $domain): ?self
    {
        return static::where('email_domain', strtolower($domain))->first();
    }
}
