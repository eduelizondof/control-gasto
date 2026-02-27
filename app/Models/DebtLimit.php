<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DebtLimit extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'name',
        'max_amount',
        'committed_amount',
        'available_amount',
    ];

    protected function casts(): array
    {
        return [
            'max_amount' => 'decimal:2',
            'committed_amount' => 'decimal:2',
            'available_amount' => 'decimal:2',
        ];
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}
