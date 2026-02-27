<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reminder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'group_id',
        'name',
        'type',
        'account_id',
        'debt_id',
        'concept_id',
        'estimated_amount',
        'frequency',
        'day_of_month',
        'specific_date',
        'advance_days',
        'auto_create_transaction',
        'is_active',
        'next_date',
        'last_executed_at',
    ];

    protected function casts(): array
    {
        return [
            'estimated_amount' => 'decimal:2',
            'specific_date' => 'date',
            'next_date' => 'date',
            'last_executed_at' => 'datetime',
            'auto_create_transaction' => 'boolean',
            'is_active' => 'boolean',
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

    public function debt(): BelongsTo
    {
        return $this->belongsTo(Debt::class);
    }

    public function concept(): BelongsTo
    {
        return $this->belongsTo(Concept::class);
    }

    // ── Scopes ──

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeUpcoming($query, int $days = 7)
    {
        return $query->where('is_active', true)
            ->whereNotNull('next_date')
            ->where('next_date', '<=', now()->addDays($days));
    }
}
