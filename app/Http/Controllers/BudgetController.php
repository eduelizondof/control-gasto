<?php

namespace App\Http\Controllers;

use App\Models\BudgetItem;
use App\Models\Group;
use App\Models\MonthlyBudget;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index(Group $group)
    {
        $budgets = $group->monthlyBudgets()
            ->with(['items' => fn($q) => $q->with(['category', 'concept', 'account'])->orderBy('sort_order')])
            ->orderByDesc('is_active')
            ->orderByDesc('created_at')
            ->get();

        // Calculate actual spending per concept and category for this month
        $expensesByConcept = $group->transactions()
            ->confirmed()
            ->thisMonth()
            ->ofType('expense')
            ->whereNotNull('concept_id')
            ->selectRaw('concept_id, SUM(amount) as total')
            ->groupBy('concept_id')
            ->pluck('total', 'concept_id');

        $expensesByCategory = $group->transactions()
            ->confirmed()
            ->thisMonth()
            ->ofType('expense')
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

        return view('budgets.index', compact('group', 'budgets'));
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
            'is_active' => 'boolean',
            'items' => 'required|array|min:1',
            'items.*.category_id' => 'required|exists:categories,id',
            'items.*.concept_id' => 'nullable|exists:concepts,id',
            'items.*.custom_name' => 'nullable|string|max:150',
            'items.*.estimated_amount' => 'required|numeric|min:0',
            'items.*.frequency' => 'required|in:monthly,bimonthly,quarterly,semiannual,annual',
            'items.*.account_id' => 'nullable|exists:accounts,id',
            'items.*.is_fixed' => 'boolean',
        ]);

        // Deactivate other budgets if this one is active
        if ($request->boolean('is_active', true)) {
            $group->monthlyBudgets()->update(['is_active' => false]);
        }

        $budget = MonthlyBudget::create([
            'group_id' => $group->id,
            'name' => $validated['name'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        foreach ($validated['items'] as $index => $itemData) {
            $divisor = BudgetItem::frequencyDivisor($itemData['frequency']);

            BudgetItem::create([
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
            ]);
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
            'is_active' => 'boolean',
        ]);

        if ($request->boolean('is_active') && !$budget->is_active) {
            $group->monthlyBudgets()->where('id', '!=', $budget->id)->update(['is_active' => false]);
        }

        $budget->update([
            'name' => $validated['name'],
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
            'custom_name' => 'nullable|string|max:150',
            'estimated_amount' => 'required|numeric|min:0',
            'frequency' => 'required|in:monthly,bimonthly,quarterly,semiannual,annual',
        ]);

        $divisor = BudgetItem::frequencyDivisor($validated['frequency']);
        $maxSort = $budget->items()->max('sort_order') ?? 0;

        BudgetItem::create([
            'monthly_budget_id' => $budget->id,
            'category_id' => $validated['category_id'],
            'custom_name' => $validated['custom_name'],
            'estimated_amount' => $validated['estimated_amount'],
            'frequency' => $validated['frequency'],
            'monthly_amount' => round($validated['estimated_amount'] / $divisor, 2),
            'sort_order' => $maxSort + 1,
        ]);

        return redirect()->route('budgets.edit', [$group, $budget])
            ->with('success', 'Item agregado exitosamente.');
    }

    public function updateItem(Request $request, Group $group, MonthlyBudget $budget, BudgetItem $item)
    {
        $validated = $request->validate([
            'estimated_amount' => 'required|numeric|min:0',
            'frequency' => 'required|in:monthly,bimonthly,quarterly,semiannual,annual',
        ]);

        $divisor = BudgetItem::frequencyDivisor($validated['frequency']);

        $item->update([
            'estimated_amount' => $validated['estimated_amount'],
            'frequency' => $validated['frequency'],
            'monthly_amount' => round($validated['estimated_amount'] / $divisor, 2),
        ]);

        return redirect()->route('budgets.edit', [$group, $budget])
            ->with('success', 'Item actualizado exitosamente.');
    }

    public function deleteItem(Group $group, MonthlyBudget $budget, BudgetItem $item)
    {
        $item->delete();

        return redirect()->route('budgets.edit', [$group, $budget])
            ->with('success', 'Item eliminado exitosamente.');
    }
}
