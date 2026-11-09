<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Poli extends Model
{
    use HasFactory;

    protected $table = 'tbl_polies';

    protected $fillable = [
        'name',
        'slug',
        'code_prefix',
        'doctor_name',
        'room',
        'open_time',
        'close_time',
        'daily_quota',
        'icon_path',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'daily_quota' => 'integer',
    ];

    public function queues(): HasMany
    {
        return $this->hasMany(Queue::class, 'poli_id');
    }

    public function todayQueuesCount(): int
    {
        return $this->queues()
            ->whereDate('visit_date', today())
            ->count();
    }

    public function remainingQuotaToday(): int
    {
        $used = $this->todayQueuesCount();
        return max(0, $this->daily_quota - $used);
    }

    public function currentQueueNumberToday(): ?string
    {
        $current = $this->queues()
            ->whereDate('visit_date', today())
            ->where('status', 'in_progress')
            ->first();

        if ($current) {
            return $current->queue_code;
        }

        $lastCompleted = $this->queues()
            ->whereDate('visit_date', today())
            ->where('status', 'completed')
            ->orderByDesc('queue_number')
            ->first();

        return $lastCompleted?->queue_code ?? '-';
    }
}
