<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Debt extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'group_id',
        'account_id',
        'name',
        'type',
        'total_amount',
        'paid_amount',
        'outstanding_balance',
        'interest_rate',
        'total_payments',
        'payments_made',
        'payment_amount',
        'start_date',
        'end_date',
        'next_payment_date',
        'cutoff_day',
        'payment_day',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'outstanding_balance' => 'decimal:2',
            'interest_rate' => 'decimal:2',
            'payment_amount' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
            'next_payment_date' => 'date',
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

    public function payments(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    // ── Scopes ──

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // ── Helpers ──

    public function getProgressPercentAttribute(): float
    {
        if ($this->total_amount <= 0) {
            return 100;
        }

        return round(($this->paid_amount / $this->total_amount) * 100, 1);
    }
}
