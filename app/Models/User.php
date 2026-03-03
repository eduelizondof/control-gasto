<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Notification;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ── Relationships ──

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class)
            ->withPivot('role', 'invited_by', 'joined_at', 'is_active', 'status')
            ->wherePivot('is_active', true);
    }

    /**
     * Groups where the user has a pending invitation.
     */
    public function pendingInvitations(): BelongsToMany
    {
        return $this->belongsToMany(Group::class)
            ->withPivot('role', 'invited_by', 'joined_at', 'is_active', 'status')
            ->wherePivot('status', 'pending');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function notifications(): BelongsToMany
    {
        return $this->belongsToMany(Notification::class)
            ->withPivot('read_at')
            ->orderByDesc('created_at')
            ->withTimestamps();
    }

    /**
     * Get the user's current active group (first one or selected).
     */
    public function currentGroup(): ?Group
    {
        return $this->groups()->first();
    }
}
