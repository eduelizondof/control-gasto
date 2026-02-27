<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeriodSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'account_id',
        'period',
        'start_date',
        'end_date',
        'starting_balance',
        'ending_balance',
        'total_income',
        'total_expenses',
        'total_savings',
        'total_transfers',
        'estimated_expenses',
        'difference',
        'is_closed',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'starting_balance' => 'decimal:2',
            'ending_balance' => 'decimal:2',
            'total_income' => 'decimal:2',
            'total_expenses' => 'decimal:2',
            'total_savings' => 'decimal:2',
            'total_transfers' => 'decimal:2',
            'estimated_expenses' => 'decimal:2',
            'difference' => 'decimal:2',
            'is_closed' => 'boolean',
        ];
    }

    // ── Relationships ──

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    // ── Scopes ──

    public function scopeOpen($query)
    {
        return $query->where('is_closed', false);
    }

    public function scopeForPeriod($query, string $period)
    {
        return $query->where('period', $period);
    }
}
