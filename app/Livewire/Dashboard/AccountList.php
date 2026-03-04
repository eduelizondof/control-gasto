<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Group;

class AccountList extends Component
{
    public Group $group;

    public function render()
    {
        $accounts = $this->group->accounts()->active()->orderBy('sort_order')->get();

        $dailyAccounts = [
            'Efectivo' => $accounts->where('type', 'cash'),
            'Débito' => $accounts->where('type', 'debit'),
            'Crédito' => $accounts->where('type', 'credit'),
            'Otras' => $accounts->whereNotIn('type', ['cash', 'debit', 'credit', 'savings', 'investment', 'emergency', 'fund']),
        ];

        $savingsAccounts = [
            'Ahorro' => $accounts->where('type', 'savings'),
            'Inversión' => $accounts->where('type', 'investment'),
            'Emergencias' => $accounts->where('type', 'emergency'),
            'Fondo' => $accounts->where('type', 'fund'),
        ];

        $dailyAccounts = array_filter($dailyAccounts, fn($group) => $group->isNotEmpty());
        $savingsAccounts = array_filter($savingsAccounts, fn($group) => $group->isNotEmpty());

        return view('livewire.dashboard.account-list', [
            'dailyAccounts' => $dailyAccounts,
            'savingsAccounts' => $savingsAccounts,
        ]);
    }
}

