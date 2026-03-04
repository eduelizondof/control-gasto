<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Group;

class SummaryCards extends Component
{
    public Group $group;

    public function render()
    {
        $accounts = $this->group->accounts()->active()->get();
        $totalBalance = $accounts->where('include_in_total', true)->sum('current_balance');

        $monthlyIncome = $this->group->transactions()
            ->confirmed()
            ->thisMonth()
            ->ofType('income')
            ->sum('amount');

        $monthlyExpenses = $this->group->transactions()
            ->confirmed()
            ->thisMonth()
            ->ofType('expense')
            ->sum('amount');

        $monthlySavings = $this->group->transactions()
            ->confirmed()
            ->thisMonth()
            ->ofType('savings')
            ->sum('amount');

        return view('livewire.dashboard.summary-cards', [
            'totalBalance' => $totalBalance,
            'monthlyIncome' => $monthlyIncome,
            'monthlyExpenses' => $monthlyExpenses,
            'monthlySavings' => $monthlySavings,
        ]);
    }
}
