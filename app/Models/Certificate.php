<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'id', 'enrollment_id', 'issued_by', 'nomor_sertifikat',
        'pdf_url', 'issued_at',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
    ];

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class, 'enrollment_id');
    }

    public function issuer()
    {
        return $this->belongsTo(Mitra::class, 'issued_by');
    }

    /**
     * Generate nomor sertifikat unik: KI-YYYY-XXXX.
     */
    public static function generateNomor(): string
    {
        $year  = date('Y');
        $last  = static::where('nomor_sertifikat', 'like', "KI-{$year}-%")
                       ->orderByDesc('nomor_sertifikat')
                       ->value('nomor_sertifikat');

        $seq = $last ? ((int) substr($last, -4)) + 1 : 1;
        return sprintf('KI-%s-%04d', $year, $seq);
    }
}
