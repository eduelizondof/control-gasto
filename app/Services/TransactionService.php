<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    /**
     * Create a transaction and update account balances.
     */
    public function create(array $data): Transaction
    {
        return DB::transaction(function () use ($data) {
            $transaction = Transaction::create($data);
            $this->updateBalances($transaction);

            return $transaction;
        });
    }

    /**
     * Update a transaction and recalculate account balances.
     */
    public function update(Transaction $transaction, array $data): Transaction
    {
        return DB::transaction(function () use ($transaction, $data) {
            // Reverse previous balance changes
            $this->reverseBalances($transaction);

            $transaction->update($data);
            $transaction->refresh();

            // Apply new balance changes
            $this->updateBalances($transaction);

            return $transaction;
        });
    }

    /**
     * Delete a transaction and reverse balance changes.
     */
    public function delete(Transaction $transaction): bool
    {
        return DB::transaction(function () use ($transaction) {
            $this->reverseBalances($transaction);

            return $transaction->delete();
        });
    }

    /**
     * Update account balances based on transaction type.
     */
    private function updateBalances(Transaction $transaction): void
    {
        if ($transaction->status !== 'confirmed') {
            return;
        }

        $sourceAccount = Account::find($transaction->source_account_id);

        match ($transaction->type) {
            'income' => $sourceAccount?->increment('current_balance', $transaction->amount),
            'expense' => $sourceAccount?->decrement('current_balance', $transaction->amount),
            'savings' => $this->handleTransfer($transaction, $sourceAccount),
            'transfer' => $this->handleTransfer($transaction, $sourceAccount),
            'adjustment' => $sourceAccount?->update(['current_balance' => $transaction->amount]),
            default => null,
        };
    }

    /**
     * Reverse balance changes for a transaction.
     */
    private function reverseBalances(Transaction $transaction): void
    {
        if ($transaction->status !== 'confirmed') {
            return;
        }

        $sourceAccount = Account::find($transaction->source_account_id);

        match ($transaction->type) {
            'income' => $sourceAccount?->decrement('current_balance', $transaction->amount),
            'expense' => $sourceAccount?->increment('current_balance', $transaction->amount),
            'savings', 'transfer' => $this->reverseTransfer($transaction, $sourceAccount),
            default => null,
        };
    }

    private function handleTransfer(Transaction $transaction, ?Account $sourceAccount): void
    {
        $sourceAccount?->decrement('current_balance', $transaction->amount);

        if ($transaction->destination_account_id) {
            Account::where('id', $transaction->destination_account_id)
                ->increment('current_balance', $transaction->amount);
        }
    }

    private function reverseTransfer(Transaction $transaction, ?Account $sourceAccount): void
    {
        $sourceAccount?->increment('current_balance', $transaction->amount);

        if ($transaction->destination_account_id) {
            Account::where('id', $transaction->destination_account_id)
                ->decrement('current_balance', $transaction->amount);
        }
    }
}
