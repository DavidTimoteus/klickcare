<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_ADMIN = 1;
    public const ROLE_STAFF = 2; // Dosen & Tenaga Kependidikan
    public const ROLE_STUDENT = 3; // Mahasiswa

    protected $table = 'tbl_users';

    protected $fillable = [
        'name',
        'noId',
        'email',
        'password',
        'gender',
        'birthDate',
        'address',
        'phoneNumber',
        'medical_record_number',
        'allergies',
        'blood_type',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'birthDate' => 'date',
        'role' => 'integer',
        'password' => 'hashed',
    ];

    public function queues(): HasMany
    {
        return $this->hasMany(Queue::class, 'user_id');
    }

    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class, 'user_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isStaff(): bool
    {
        return $this->role === self::ROLE_STAFF;
    }

    public function isStudent(): bool
    {
        return $this->role === self::ROLE_STUDENT;
    }

    /**
     * Nama Tampilan Bersih (tanpa kurung peran tambahan di navbar)
     */
    public function getDisplayNameAttribute(): string
    {
        $clean = preg_replace('/\s*\(.*?\)\s*/', '', $this->name);
        return trim($clean) ?: $this->name;
    }

    /**
     * Label Singkat Peran untuk Badge Navbar
     */
    public function getRoleBadgeLabelAttribute(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN => 'Petugas Medis',
            self::ROLE_STAFF => 'Dosen / Staf',
            self::ROLE_STUDENT => 'Mahasiswa',
            default => 'Pengguna',
        };
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN => 'Admin / Petugas Medis',
            self::ROLE_STAFF => 'Dosen / Tenaga Kependidikan',
            self::ROLE_STUDENT => 'Mahasiswa Polinema',
            default => 'Pengguna',
        };
    }

    /**
     * Nomor Rekam Medis (No. RM) Permanen Pasien
     */
    public function getFormattedMedicalRecordNumberAttribute(): string
    {
        return $this->medical_record_number ?: ('RM-' . date('y') . '-' . str_pad((string) $this->id, 4, '0', STR_PAD_LEFT));
    }
}
