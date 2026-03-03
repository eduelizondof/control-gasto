<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
            ->take(5)
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
        $q1Paid = 0;
        $q2Load = 0;
        $q2Paid = 0;

        foreach ($remindersThisMonth as $r) {
            $isPaid = false;
            if ($r->category_id && $r->concept_id) {
                $isPaid = $thisMonthTransactions->some(fn($t) => $t->category_id == $r->category_id && $t->concept_id == $r->concept_id);
            } elseif ($r->concept_id) {
                $isPaid = $thisMonthTransactions->some(fn($t) => $t->concept_id == $r->concept_id);
            } elseif ($r->category_id) {
                $isPaid = $thisMonthTransactions->some(fn($t) => $t->category_id == $r->category_id);
            }

            $amount = (float) $r->estimated_amount;
            $day = $r->next_date->day;

            if ($day <= 15) {
                $q1Load += $amount;
                if ($isPaid) $q1Paid += $amount;
            } else {
                $q2Load += $amount;
                if ($isPaid) $q2Paid += $amount;
            }
        }

        $q1Pending = max(0, $q1Load - $q1Paid);
        $q2Pending = max(0, $q2Load - $q2Paid);

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

        // Expenses by category for current month
        $expensesByCategory = $group->transactions()
            ->confirmed()
            ->thisMonth()
            ->ofType('expense')
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->with('category')
            ->get()
            ->sortByDesc('total');

        // Active debts
        $activeDebts = $group->debts()->active()->get();

        // Budget configuration for income comparison
        $budgetConfig = $group->getBudgetConfiguration();
        $configuredIncome = (float) $budgetConfig->total_monthly_income;
        $configuredFixedIncome = (float) $budgetConfig->fixed_monthly_income;

        // 12-month income/expense/savings data
        $twelveMonthsAgo = Carbon::now()->subMonths(11)->startOfMonth();

        $monthlyData = $group->transactions()
            ->confirmed()
            ->where('date', '>=', $twelveMonthsAgo)
            ->select(
                DB::raw("DATE_FORMAT(date, '%Y-%m') as period"),
                DB::raw("SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as income"),
                DB::raw("SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expenses"),
                DB::raw("SUM(CASE WHEN type = 'savings' THEN amount ELSE 0 END) as savings")
            )
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->keyBy('period');

        // Build 12-month chart data (fill missing months with 0)
        $chartLabels = [];
        $chartIncome = [];
        $chartExpenses = [];
        $chartSavings = [];
        $totalIncomeSum = 0;
        $monthsWithIncome = 0;

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $period = $date->format('Y-m');
            $chartLabels[] = $date->translatedFormat('M Y');

            $data = $monthlyData->get($period);
            $inc = $data ? (float) $data->income : 0;
            $exp = $data ? (float) $data->expenses : 0;
            $sav = $data ? (float) $data->savings : 0;

            $chartIncome[] = $inc;
            $chartExpenses[] = $exp;
            $chartSavings[] = $sav;

            if ($inc > 0) {
                $totalIncomeSum += $inc;
                $monthsWithIncome++;
            }
        }

        $avgIncome12m = $monthsWithIncome > 0 ? $totalIncomeSum / $monthsWithIncome : 0;
        $incomeDiff = $configuredIncome > 0 ? $avgIncome12m - $configuredIncome : 0;
        $incomeDiffPercent = $configuredIncome > 0 ? round(($incomeDiff / $configuredIncome) * 100, 1) : 0;

        // Expenses by category 12 months (Pie chart)
        $expensesByCategory12m = $group->transactions()
            ->confirmed()
            ->where('date', '>=', $twelveMonthsAgo)
            ->ofType('expense')
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->with('category')
            ->get()
            ->sortByDesc('total');

        $chartCategoryLabels = [];
        $chartCategoryData = [];
        $chartCategoryColors = [];

        foreach ($expensesByCategory12m as $exp) {
            $chartCategoryLabels[] = $exp->category->name ?? 'Sin categoría';
            $chartCategoryData[] = (float) $exp->total;
            $chartCategoryColors[] = $exp->category->color ?? '#9ca3af';
        }

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
            'q1Pending',
            'q2Pending',
            'q1Paid',
            'q2Paid',
            'budgetConfig',
            'configuredIncome',
            'configuredFixedIncome',
            'avgIncome12m',
            'incomeDiff',
            'incomeDiffPercent',
            'chartLabels',
            'chartIncome',
            'chartExpenses',
            'chartSavings',
            'chartCategoryLabels',
            'chartCategoryData',
            'chartCategoryColors'
        ));
    }
}
