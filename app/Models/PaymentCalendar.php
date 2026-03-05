<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentCalendar extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'account_id',
        'person_name',
        'concept',
        'amount',
        'payment_date',
        'transaction_id',
        'category_id',
        'concept_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_date' => 'date',
        ];
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function conceptRelation(): BelongsTo
    {
        return $this->belongsTo(Concept::class, 'concept_id');
    }
}
