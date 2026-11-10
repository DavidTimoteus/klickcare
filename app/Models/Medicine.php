<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medicine extends Model
{
    use HasFactory;

    protected $table = 'tbl_medicines';

    protected $fillable = [
        'code',
        'name',
        'category',
        'unit',
        'stock',
        'min_stock',
        'description',
        'is_active',
    ];

    protected $casts = [
        'stock' => 'integer',
        'min_stock' => 'integer',
        'is_active' => 'boolean',
    ];

    public function prescriptionItems(): HasMany
    {
        return $this->hasMany(PrescriptionItem::class, 'medicine_id');
    }

    /**
     * Apakah stok habis (0 atau kurang)
     */
    public function isOutOfStock(): bool
    {
        return $this->stock <= 0;
    }

    /**
     * Apakah stok berada di bawah batas minimum
     */
    public function isLowStock(): bool
    {
        return $this->stock <= $this->min_stock;
    }

    /**
     * Badge status ketersediaan stok
     */
    public function getStockBadgeClassAttribute(): string
    {
        if ($this->stock <= 0) {
            return 'bg-danger text-white';
        }
        if ($this->isLowStock()) {
            return 'bg-warning-subtle text-warning-emphasis border border-warning';
        }
        return 'bg-success-subtle text-success border border-success-subtle';
    }

    public function getStockStatusLabelAttribute(): string
    {
        if ($this->stock <= 0) {
            return 'Stok Habis';
        }
        if ($this->isLowStock()) {
            return 'Stok Menipis';
        }
        return 'Tersedia';
    }
}
