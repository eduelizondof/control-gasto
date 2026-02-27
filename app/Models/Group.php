<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'created_by',
    ];

    // ── Relationships ──

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role', 'invited_by', 'joined_at', 'is_active', 'status')
            ->wherePivot('is_active', true);
    }

    /**
     * Users with pending invitations to this group.
     */
    public function pendingUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role', 'invited_by', 'joined_at', 'is_active', 'status')
            ->wherePivot('status', 'pending');
    }

    /**
     * All users regardless of status (for uniqueness checks).
     */
    public function allUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role', 'invited_by', 'joined_at', 'is_active', 'status');
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function concepts(): HasMany
    {
        return $this->hasMany(Concept::class);
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function debts(): HasMany
    {
        return $this->hasMany(Debt::class);
    }

    public function debtLimits(): HasMany
    {
        return $this->hasMany(DebtLimit::class);
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(Reminder::class);
    }

    public function monthlyBudgets(): HasMany
    {
        return $this->hasMany(MonthlyBudget::class);
    }

    public function periodSnapshots(): HasMany
    {
        return $this->hasMany(PeriodSnapshot::class);
    }
}
