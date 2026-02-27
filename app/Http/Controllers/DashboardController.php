<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $group = $user->groups()->first();

        if (!$group) {
            return redirect()->route('groups.create')
                ->with('info', 'Crea tu primer grupo para empezar.');
        }

        $currentPeriod = now()->format('Y-m');

        // Summary data
        $accounts = $group->accounts()->active()->get();

        $totalBalance = $accounts->where('include_in_total', true)->sum('current_balance');

        $monthlyIncome = $group->transactions()
            ->confirmed()
            ->thisMonth()
            ->ofType('income')
            ->sum('amount');

        $monthlyExpenses = $group->transactions()
            ->confirmed()
            ->thisMonth()
            ->ofType('expense')
            ->sum('amount');

        $monthlySavings = $group->transactions()
            ->confirmed()
            ->thisMonth()
            ->ofType('savings')
            ->sum('amount');

        // Recent transactions
        $recentTransactions = $group->transactions()
            ->with(['category', 'sourceAccount', 'concept'])
            ->confirmed()
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        // Upcoming reminders
        $upcomingReminders = $group->reminders()
            ->active()
            ->whereNotNull('next_date')
            ->orderBy('next_date')
            ->take(5)
            ->get();

        // Budget info
        $activeBudget = $group->monthlyBudgets()->where('is_active', true)->first();
        $budgetTotal = $activeBudget?->items()->where('is_active', true)->sum('monthly_amount') ?? 0;

        // Expenses by category for chart
        $expensesByCategory = $group->transactions()
            ->confirmed()
            ->thisMonth()
            ->ofType('expense')
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->with('category')
            ->get();

        // Active debts
        $activeDebts = $group->debts()->active()->get();

        return view('dashboard', compact(
            'group',
            'accounts',
            'totalBalance',
            'monthlyIncome',
            'monthlyExpenses',
            'monthlySavings',
            'recentTransactions',
            'upcomingReminders',
            'activeBudget',
            'budgetTotal',
            'expensesByCategory',
            'activeDebts',
            'currentPeriod',
        ));
    }
}
