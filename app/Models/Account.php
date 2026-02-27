<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'group_id',
        'name',
        'type',
        'bank',
        'currency',
        'initial_balance',
        'current_balance',
        'estimated_balance',
        'cutoff_balance',
        'credit_limit',
        'cutoff_day',
        'payment_day',
        'color',
        'icon',
        'include_in_total',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'initial_balance' => 'decimal:2',
            'current_balance' => 'decimal:2',
            'estimated_balance' => 'decimal:2',
            'cutoff_balance' => 'decimal:2',
            'credit_limit' => 'decimal:2',
            'include_in_total' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    // ── Relationships ──

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'source_account_id');
    }

    public function incomingTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'destination_account_id');
    }

    public function debts(): HasMany
    {
        return $this->hasMany(Debt::class);
    }

    // ── Scopes ──

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeIncludedInTotal($query)
    {
        return $query->where('include_in_total', true);
    }

    // ── Helpers ──

    public function getAvailableCreditAttribute(): ?float
    {
        if ($this->type !== 'credit' || !$this->credit_limit) {
            return null;
        }

        return $this->credit_limit - abs($this->current_balance);
    }

    public function getTypeLabelsAttribute(): string
    {
        return match ($this->type) {
            'cash' => 'Efectivo',
            'debit' => 'Débito',
            'credit' => 'Crédito',
            'investment' => 'Inversión',
            'savings' => 'Ahorro',
            'emergency' => 'Emergencias',
            'fund' => 'Fondo',
            default => $this->type,
        };
    }
}
