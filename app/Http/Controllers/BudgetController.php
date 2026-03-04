<?php

namespace App\Http\Controllers;

use App\Models\BudgetItem;
use App\Models\Group;
use App\Models\MonthlyBudget;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index(Request $request, Group $group)
    {
        $year = $request->get('year', date('Y'));

        $budgets = $group->monthlyBudgets()
            ->where('year', $year)
            ->with(['items' => fn($q) => $q->with(['category', 'concept', 'account'])->orderBy('sort_order')])
            ->orderByDesc('is_active')
            ->orderByDesc('created_at')
            ->get();

        // Calculate actual spending per concept and category for this year
        $expensesByConcept = $group->transactions()
            ->confirmed()
            ->whereYear('date', $year)
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
            ->selectRaw('concept_id, SUM(amount) as total')
            ->groupBy('concept_id')
            ->pluck('total', 'concept_id');

        $expensesByCategory = $group->transactions()
            ->confirmed()
            ->whereYear('date', $year)
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
            ->pluck('total', 'category_id');

        // Attach actual_spent to each budget item
        foreach ($budgets as $budget) {
            foreach ($budget->items as $item) {
                if ($item->concept_id && $expensesByConcept->has($item->concept_id)) {
                    $item->actual_spent = (float) $expensesByConcept->get($item->concept_id);
                } elseif (!$item->concept_id) {
                    $item->actual_spent = (float) ($expensesByCategory->get($item->category_id, 0));
                } else {
                    $item->actual_spent = 0;
                }
            }
        }

        return view('budgets.index', compact('group', 'budgets', 'year'));
    }

    public function create(Group $group)
    {
        $categories = $group->categories()->orderBy('type')->orderBy('name')->get();
        $concepts = $group->concepts()->orderBy('name')->get();
        $accounts = $group->accounts()->active()->get();

        return view('budgets.create', compact('group', 'categories', 'concepts', 'accounts'));
    }

    public function store(Request $request, Group $group)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'year' => 'required|integer|min:2000|max:2100',
            'is_active' => 'boolean',
            'items' => 'required|array|min:1',
            'items.*.category_id' => 'required|exists:categories,id',
            'items.*.concept_id' => 'nullable|exists:concepts,id',
            'items.*.custom_name' => 'nullable|string|max:150',
            'items.*.estimated_amount' => 'required|numeric|min:0',
            'items.*.frequency' => 'required|in:monthly,bimonthly,quarterly,semiannual,annual',
            'items.*.account_id' => 'nullable|exists:accounts,id',
            'items.*.is_fixed' => 'boolean',
            'items.*.payment_month' => 'nullable|integer|min:1|max:12',
            'items.*.payment_day' => 'nullable|integer|min:1|max:31',
        ]);

        // Deactivate other budgets if this one is active
        if ($request->boolean('is_active', true)) {
            $group->monthlyBudgets()->update(['is_active' => false]);
        }

        $budget = MonthlyBudget::create([
            'group_id' => $group->id,
            'name' => $validated['name'],
            'year' => $validated['year'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        foreach ($validated['items'] as $index => $itemData) {
            $divisor = BudgetItem::frequencyDivisor($itemData['frequency']);

            $budgetItem = BudgetItem::create([
                'monthly_budget_id' => $budget->id,
                'category_id' => $itemData['category_id'],
                'concept_id' => $itemData['concept_id'] ?? null,
                'custom_name' => $itemData['custom_name'] ?? null,
                'estimated_amount' => $itemData['estimated_amount'],
                'frequency' => $itemData['frequency'],
                'monthly_amount' => round($itemData['estimated_amount'] / $divisor, 2),
                'account_id' => $itemData['account_id'] ?? null,
                'is_fixed' => $itemData['is_fixed'] ?? true,
                'sort_order' => $index,
                'payment_month' => $itemData['payment_month'] ?? null,
                'payment_day' => $itemData['payment_day'] ?? null,
            ]);

            $this->syncReminder($group->id, $budgetItem, $budget->year);
        }

        return redirect()->route('budgets.index', $group)
            ->with('success', 'Presupuesto creado exitosamente.');
    }

    public function edit(Group $group, MonthlyBudget $budget)
    {
        $budget->load(['items' => fn($q) => $q->with(['category', 'concept'])->orderBy('sort_order')]);

        $categories = $group->categories()->orderBy('type')->orderBy('name')->get();
        $concepts = $group->concepts()->orderBy('name')->get();
        $accounts = $group->accounts()->active()->get();

        return view('budgets.edit', compact('group', 'budget', 'categories', 'concepts', 'accounts'));
    }

    public function update(Request $request, Group $group, MonthlyBudget $budget)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'year' => 'required|integer|min:2000|max:2100',
            'is_active' => 'boolean',
        ]);

        if ($request->boolean('is_active') && !$budget->is_active) {
            $group->monthlyBudgets()->where('id', '!=', $budget->id)->update(['is_active' => false]);
        }

        $budget->update([
            'name' => $validated['name'],
            'year' => $validated['year'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('budgets.index', $group)
            ->with('success', 'Presupuesto actualizado exitosamente.');
    }

    public function destroy(Group $group, MonthlyBudget $budget)
    {
        $budget->items()->delete();
        $budget->delete();

        return redirect()->route('budgets.index', $group)
            ->with('success', 'Presupuesto eliminado exitosamente.');
    }

    public function addItem(Request $request, Group $group, MonthlyBudget $budget)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'concept_id' => 'nullable|exists:concepts,id',
            'estimated_amount' => 'required|numeric|min:0',
            'frequency' => 'required|in:monthly,bimonthly,quarterly,semiannual,annual',
            'payment_month' => 'nullable|integer|min:1|max:12',
            'payment_day' => 'nullable|integer|min:1|max:31',
        ]);

        $divisor = BudgetItem::frequencyDivisor($validated['frequency']);
        $maxSort = $budget->items()->max('sort_order') ?? 0;

        $budgetItem = BudgetItem::create([
            'monthly_budget_id' => $budget->id,
            'category_id' => $validated['category_id'],
            'concept_id' => $validated['concept_id'] ?? null,
            'estimated_amount' => $validated['estimated_amount'],
            'frequency' => $validated['frequency'],
            'monthly_amount' => round($validated['estimated_amount'] / $divisor, 2),
            'sort_order' => $maxSort + 1,
            'payment_month' => $validated['payment_month'] ?? null,
            'payment_day' => $validated['payment_day'] ?? null,
        ]);

        $this->syncReminder($group->id, $budgetItem, $budget->year);

        return redirect()->route('budgets.edit', [$group, $budget])
            ->with('success', 'Item agregado exitosamente.');
    }

    public function updateItem(Request $request, Group $group, MonthlyBudget $budget, BudgetItem $item)
    {
        $validated = $request->validate([
            'estimated_amount' => 'required|numeric|min:0',
            'frequency' => 'required|in:monthly,bimonthly,quarterly,semiannual,annual',
            'payment_month' => 'nullable|integer|min:1|max:12',
            'payment_day' => 'nullable|integer|min:1|max:31',
        ]);

        $divisor = BudgetItem::frequencyDivisor($validated['frequency']);

        $item->update([
            'estimated_amount' => $validated['estimated_amount'],
            'frequency' => $validated['frequency'],
            'monthly_amount' => round($validated['estimated_amount'] / $divisor, 2),
            'payment_month' => $validated['payment_month'] ?? null,
            'payment_day' => $validated['payment_day'] ?? null,
        ]);

        $this->syncReminder($group->id, $item, $budget->year);

        return redirect()->route('budgets.edit', [$group, $budget])
            ->with('success', 'Item actualizado exitosamente.');
    }

    public function deleteItem(Group $group, MonthlyBudget $budget, BudgetItem $item)
    {
        $item->delete();

        return redirect()->route('budgets.edit', [$group, $budget])
            ->with('success', 'Item eliminado exitosamente.');
    }

    private function syncReminder($groupId, BudgetItem $item, $year)
    {
        if ($item->payment_day) {
            $name = $item->custom_name ?? ($item->concept_id ? $item->concept->name : $item->category->name);
            $name .= ' (Presupuesto)';

            $attributes = [
                'name' => $name,
                'type' => 'custom',
                'account_id' => $item->account_id,
                'estimated_amount' => $item->estimated_amount,
                'frequency' => $item->frequency,
                'auto_create_transaction' => false,
                'is_active' => $item->is_active ?? true,
            ];

            if (in_array($item->frequency, ['annual', 'semiannual']) && $item->payment_month) {
                // If it's a specific date in the budget year
                try {
                    $date = \Carbon\Carbon::createFromDate($year, $item->payment_month, $item->payment_day);
                    $attributes['specific_date'] = $date->format('Y-m-d');
                    
                    if ($date->year < now()->year || ($date->year == now()->year && $date->month < now()->month)) {
                        // Already passed this year, point to next iteration if it's recurring
                        if ($item->frequency === 'annual') {
                            $date->addYear();
                        } elseif ($item->frequency === 'semiannual') {
                            $date->addMonths(6);
                        }
                    }
                    $attributes['next_date'] = $date->format('Y-m-d');
                    $attributes['day_of_month'] = null;
                } catch (\Exception $e) {
                    // Ignore date errors
                }
            } else {
                $attributes['day_of_month'] = $item->payment_day;
                $attributes['specific_date'] = null;
                $day = min((int) $item->payment_day, 28);
                $next = now()->day($day);
                if ($next->isPast()) {
                    $next->addMonth();
                }
                $attributes['next_date'] = $next->format('Y-m-d');
            }

            \App\Models\Reminder::updateOrCreate(
                [
                    'group_id' => $groupId,
                    'category_id' => $item->category_id,
                    'concept_id' => $item->concept_id,
                ],
                $attributes
            );
        }
    }
}
