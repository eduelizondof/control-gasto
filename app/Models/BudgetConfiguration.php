<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'necessities_percentage',
        'debts_percentage',
        'future_percentage',
        'desires_percentage',
    ];

    /**
     * Get the group that owns the budget configuration.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}
