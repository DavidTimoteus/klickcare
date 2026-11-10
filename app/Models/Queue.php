<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Queue extends Model
{
    use HasFactory;

    protected $table = 'tbl_queues';

    protected $fillable = [
        'user_id',
        'poli_id',
        'queue_code',
        'queue_number',
        'visit_date',
        'complaint',
        'status',
        'doctor_notes',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'queue_number' => 'integer',
        'complaint' => 'encrypted',
        'doctor_notes' => 'encrypted',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function poli(): BelongsTo
    {
        return $this->belongsTo(Poli::class, 'poli_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'waiting' => 'Menunggu Panggilan',
            'in_progress' => 'Sedang Diperiksa',
            'skipped' => 'Dilewati (Menunggu Panggil Ulang)',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'waiting' => 'bg-warning-subtle text-warning-emphasis border-warning',
            'in_progress' => 'bg-primary-subtle text-primary-emphasis border-primary',
            'skipped' => 'bg-secondary-subtle text-dark border-secondary',
            'completed' => 'bg-success-subtle text-success-emphasis border-success',
            'cancelled' => 'bg-danger-subtle text-danger-emphasis border-danger',
            default => 'bg-secondary-subtle text-secondary-emphasis',
        };
    }
}
