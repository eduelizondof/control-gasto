<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Group;

class BudgetOverview extends Component
{
    public Group $group;

    public function render()
    {
        $activeBudget = $this->group->monthlyBudgets()->where('is_active', true)->first();
        $budgetTotal = $activeBudget?->items()->where('is_active', true)->sum('monthly_amount') ?? 0;

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

            $expensesByConcept = $this->group->transactions()
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

            $expensesByCategory = $this->group->transactions()
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

        return view('livewire.dashboard.budget-overview', [
            'activeBudget' => $activeBudget,
            'budgetTotal' => $budgetTotal,
            'budgetSpent' => $budgetSpent,
            'budgetBreakdown' => $budgetBreakdown,
            'outOfBudgetBreakdown' => $outOfBudgetBreakdown,
            'outOfBudgetTotal' => $outOfBudgetTotal,
        ]);
    }
}
