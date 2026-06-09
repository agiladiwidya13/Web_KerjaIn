<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'id', 'enrollment_id', 'task_id', 'file_url', 'catatan',
        'status', 'feedback', 'nilai', 'reviewed_by', 'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class, 'enrollment_id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(Mentor::class, 'reviewed_by');
    }

    // ── Helpers ──────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === 'menunggu';
    }

    public function isApproved(): bool
    {
        return $this->status === 'disetujui';
    }

    public function needsRevision(): bool
    {
        return $this->status === 'revisi';
    }
}
