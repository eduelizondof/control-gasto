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
        $pendingInvitationsCount = $user->pendingInvitations()->count();

        if (!$group) {
            // If no groups but has pending invitations, redirect to invitations
            if ($pendingInvitationsCount > 0) {
                return redirect()->route('invitations.index')
                    ->with('info', 'Tienes invitaciones pendientes. ¡Acepta una para empezar o crea tu propio grupo!');
            }

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

        // Budget breakdown per concept/category
        $budgetBreakdown = collect();
        if ($activeBudget) {
            $budgetItems = $activeBudget->items()
                ->where('is_active', true)
                ->with(['concept', 'category'])
                ->orderBy('sort_order')
                ->get();

            // Get actual expenses grouped by concept_id
            $expensesByConcept = $group->transactions()
                ->confirmed()
                ->thisMonth()
                ->ofType('expense')
                ->whereNotNull('concept_id')
                ->selectRaw('concept_id, SUM(amount) as total')
                ->groupBy('concept_id')
                ->pluck('total', 'concept_id');

            // Get actual expenses grouped by category_id (fallback)
            $expensesByCategory = $group->transactions()
                ->confirmed()
                ->thisMonth()
                ->ofType('expense')
                ->selectRaw('category_id, SUM(amount) as total')
                ->groupBy('category_id')
                ->pluck('total', 'category_id');

            // Track which concept-level spending has been assigned to avoid double counting
            $usedCategorySpending = [];

            foreach ($budgetItems as $item) {
                $name = $item->concept?->name ?? $item->custom_name ?? $item->category->name;

                if ($item->concept_id && $expensesByConcept->has($item->concept_id)) {
                    $spent = (float) $expensesByConcept->get($item->concept_id);
                } elseif (!$item->concept_id) {
                    // Fallback: sum of category expenses not already assigned to a concept-level budget item
                    $spent = (float) ($expensesByCategory->get($item->category_id, 0));
                } else {
                    $spent = 0;
                }

                $budgeted = (float) $item->monthly_amount;
                $diff = $budgeted - $spent;

                $budgetBreakdown->push((object) [
                    'name' => $name,
                    'category' => $item->category,
                    'budgeted' => $budgeted,
                    'spent' => $spent,
                    'diff' => $diff,
                    'percent' => $budgeted > 0 ? min(100, round(($spent / $budgeted) * 100)) : 0,
                ]);
            }
        }

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
            'budgetBreakdown',
            'expensesByCategory',
            'activeDebts',
            'currentPeriod',
            'pendingInvitationsCount',
        ));
    }
}
