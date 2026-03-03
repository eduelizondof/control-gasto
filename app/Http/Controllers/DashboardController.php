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
            ->with(['category', 'sourceAccount', 'destinationAccount', 'concept'])
            ->confirmed()
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        // Upcoming reminders
        $upcomingRemindersRaw = $group->reminders()
            ->active()
            ->with(['category', 'concept'])
            ->whereNotNull('next_date')
            ->orderBy('next_date')
            ->get();

        // Filter out reminders already paid this month
        $thisMonthTransactions = $group->transactions()
            ->confirmed()
            ->thisMonth()
            ->get();

        $upcomingReminders = $upcomingRemindersRaw->filter(function ($reminder) use ($thisMonthTransactions) {
            // If it has both category and concept, skip if a transaction matches both
            if ($reminder->category_id && $reminder->concept_id) {
                return !$thisMonthTransactions->some(fn($t) => $t->category_id == $reminder->category_id && $t->concept_id == $reminder->concept_id);
            }
            // If it only has concept
            if ($reminder->concept_id) {
                return !$thisMonthTransactions->some(fn($t) => $t->concept_id == $reminder->concept_id);
            }
            // If it only has category
            if ($reminder->category_id) {
                return !$thisMonthTransactions->some(fn($t) => $t->category_id == $reminder->category_id);
            }
            return true;
        })->take(5);

        // Quincena Analysis
        $remindersThisMonth = $group->reminders()
            ->active()
            ->whereNotNull('next_date')
            ->whereYear('next_date', now()->year)
            ->whereMonth('next_date', now()->month)
            ->get();

        $q1Load = 0;
        $q2Load = 0;

        foreach ($remindersThisMonth as $r) {
            $day = $r->next_date->day;
            if ($day <= 15) {
                $q1Load += $r->estimated_amount;
            } else {
                $q2Load += $r->estimated_amount;
            }
        }

        // Budget info
        $activeBudget = $group->monthlyBudgets()->where('is_active', true)->first();
        $budgetTotal = $activeBudget?->items()->where('is_active', true)->sum('monthly_amount') ?? 0;

        // Budget breakdown per concept/category
        $budgetBreakdown = collect();
        $outOfBudgetBreakdown = collect();
        $budgetSpent = 0;
        $outOfBudgetTotal = 0;

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
                ->where(function ($q) {
                    $q->where('type', 'savings')
                      ->orWhere(function ($sq) {
                          $sq->where('type', 'expense')
                             ->whereHas('sourceAccount', function ($aq) {
                                 $aq->where('type', '!=', 'savings');
                             });
                      });
                })
                ->whereNotNull('concept_id')
                ->selectRaw('concept_id, MAX(category_id) as category_id, SUM(amount) as total')
                ->groupBy('concept_id')
                ->get()
                ->keyBy('concept_id');

            // Get actual expenses grouped by category_id (fallback) - ONLY for transactions without a concept
            $expensesByCategory = $group->transactions()
                ->confirmed()
                ->thisMonth()
                ->where(function ($q) {
                    $q->where('type', 'savings')
                      ->orWhere(function ($sq) {
                          $sq->where('type', 'expense')
                             ->whereHas('sourceAccount', function ($aq) {
                                 $aq->where('type', '!=', 'savings');
                             });
                      });
                })
                ->whereNull('concept_id')
                ->selectRaw('category_id, SUM(amount) as total')
                ->groupBy('category_id')
                ->get()
                ->keyBy('category_id');

            foreach ($budgetItems as $item) {
                $name = $item->concept?->name ?? $item->custom_name ?? $item->category->name;
                $spent = 0;

                if ($item->concept_id && $expensesByConcept->has($item->concept_id)) {
                    $spent = (float) $expensesByConcept->get($item->concept_id)->total;
                    $expensesByConcept->forget($item->concept_id);
                } elseif (!$item->concept_id && $expensesByCategory->has($item->category_id)) {
                    $spent = (float) $expensesByCategory->get($item->category_id)->total;
                    $expensesByCategory->forget($item->category_id);
                }

                $budgeted = (float) $item->monthly_amount;
                $diff = $budgeted - $spent;
                $budgetSpent += $spent;

                $budgetBreakdown->push((object) [
                    'name' => $name,
                    'category' => $item->category,
                    'budgeted' => $budgeted,
                    'spent' => $spent,
                    'diff' => $diff,
                    'percent' => $budgeted > 0 ? min(100, round(($spent / $budgeted) * 100)) : 0,
                ]);
            }

            // Any remaining expenses in collections are out of budget
            $remainingConcepts = \App\Models\Concept::with('category')->whereIn('id', $expensesByConcept->keys())->get()->keyBy('id');
            foreach ($expensesByConcept as $conceptId => $data) {
                $concept = $remainingConcepts->get($conceptId);
                $spent = (float) $data->total;
                $outOfBudgetTotal += $spent;
                $outOfBudgetBreakdown->push((object) [
                    'name' => $concept->name ?? 'Concepto Desconocido',
                    'category' => $concept->category ?? null,
                    'spent' => $spent,
                ]);
            }

            $remainingCategories = \App\Models\Category::whereIn('id', $expensesByCategory->keys())->get()->keyBy('id');
            foreach ($expensesByCategory as $categoryId => $data) {
                $category = $remainingCategories->get($categoryId);
                $spent = (float) $data->total;
                $outOfBudgetTotal += $spent;
                $outOfBudgetBreakdown->push((object) [
                    'name' => 'General de '.($category->name ?? 'Categoría'),
                    'category' => $category,
                    'spent' => $spent,
                ]);
            }

            $outOfBudgetBreakdown = $outOfBudgetBreakdown->sortByDesc('spent')->values();
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
            'budgetSpent',
            'budgetBreakdown',
            'outOfBudgetBreakdown',
            'outOfBudgetTotal',
            'expensesByCategory',
            'activeDebts',
            'currentPeriod',
            'pendingInvitationsCount',
            'q1Load',
            'q2Load',
        ));
    }
}
