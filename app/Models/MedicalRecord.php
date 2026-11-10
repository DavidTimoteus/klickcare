<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $table = 'tbl_medical_records';

    protected $fillable = [
        'record_code',
        'queue_id',
        'user_id',
        'poli_id',
        'doctor_name',
        'diagnosis',
        'symptoms',
        'blood_pressure',
        'temperature',
        'weight',
        'treatment_notes',
    ];

    /**
     * Enkripsi Rekam Medis (Kepatuhan UU PDP & Permenkes No. 24/2022)
     */
    protected $casts = [
        'diagnosis' => 'encrypted',
        'symptoms' => 'encrypted',
        'treatment_notes' => 'encrypted',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function poli(): BelongsTo
    {
        return $this->belongsTo(Poli::class, 'poli_id');
    }

    public function queue(): BelongsTo
    {
        return $this->belongsTo(Queue::class, 'queue_id');
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(PrescriptionItem::class, 'medical_record_id');
    }
}
