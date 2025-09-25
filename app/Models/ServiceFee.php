<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'min_amount',
        'max_amount',
        'fee_percentage',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'fee_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Faol xizmat haqi qoidalarini olish
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Tartib bo'yicha saralash
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('min_amount');
    }

    /**
     * Berilgan summa uchun mos xizmat haqi qoidasini topish
     */
    public static function getFeeForAmount($amount)
    {
        return static::active()
            ->where('min_amount', '<=', $amount)
            ->where('max_amount', '>=', $amount)
            ->ordered()
            ->first();
    }

    /**
     * Xizmat haqi miqdorini hisoblash
     */
    public function calculateFee($amount)
    {
        return ($amount * $this->fee_percentage) / 100;
    }

    /**
     * Xizmat haqi qoidasining to'liq matnini olish
     */
    public function getRangeTextAttribute()
    {
        return number_format($this->min_amount, 0, ',', ' ') . ' - ' . 
               number_format($this->max_amount, 0, ',', ' ') . ' so\'m';
    }
}
