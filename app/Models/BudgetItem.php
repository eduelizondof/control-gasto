<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BudgetItem extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = false;

    protected $fillable = [
        'monthly_budget_id',
        'concept_id',
        'custom_name',
        'category_id',
        'estimated_amount',
        'frequency',
        'monthly_amount',
        'account_id',
        'is_fixed',
        'notes',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'estimated_amount' => 'decimal:2',
            'monthly_amount' => 'decimal:2',
            'is_fixed' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    // ── Relationships ──

    public function monthlyBudget(): BelongsTo
    {
        return $this->belongsTo(MonthlyBudget::class);
    }

    public function concept(): BelongsTo
    {
        return $this->belongsTo(Concept::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    // ── Helpers ──

    public static function frequencyDivisor(string $frequency): int
    {
        return match ($frequency) {
            'monthly' => 1,
            'bimonthly' => 2,
            'quarterly' => 3,
            'semiannual' => 6,
            'annual' => 12,
            default => 1,
        };
    }

    /**
     * Auto-calculate monthly amount from estimated amount and frequency.
     */
    public function calculateMonthlyAmount(): float
    {
        $divisor = self::frequencyDivisor($this->frequency);

        return round($this->estimated_amount / $divisor, 2);
    }
}
