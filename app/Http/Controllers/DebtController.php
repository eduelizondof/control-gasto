<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Models\Group;
use Illuminate\Http\Request;

class DebtController extends Controller
{
    public function index(Group $group)
    {
        $debts = $group->debts()->with('account')->orderByDesc('created_at')->get();
        $debtLimits = $group->debtLimits()->get();

        // Budget configuration for debt capacity
        $budgetConfig = $group->getBudgetConfiguration();
        $totalIncome = (float) $budgetConfig->total_monthly_income;
        $debtPercent = (float) $budgetConfig->debts_percentage;
        $debtCapacity = $totalIncome * $debtPercent / 100;

        // Active debts calculations
        $activeDebts = $debts->where('status', 'active');
        $totalMonthlyPayments = $activeDebts->sum('payment_amount');
        $availableCapacity = $debtCapacity - $totalMonthlyPayments;
        $usedPercent = $debtCapacity > 0 ? min(100, round(($totalMonthlyPayments / $debtCapacity) * 100)) : 0;

        // Capacity timeline: when each debt frees up capacity
        $capacityTimeline = $activeDebts
            ->filter(fn($d) => $d->end_date && $d->payment_amount > 0)
            ->sortBy('end_date')
            ->values()
            ->map(function ($debt) use (&$availableCapacity, $debtCapacity) {
                // This builds a running total of available capacity after each debt ends
                return (object) [
                    'date' => $debt->end_date,
                    'name' => $debt->name,
                    'freed_amount' => (float) $debt->payment_amount,
                    'remaining_payments' => $debt->total_payments
                        ? max(0, $debt->total_payments - $debt->payments_made)
                        : null,
                ];
            });

        // Calculate running capacity
        $runningCapacity = $debtCapacity - $totalMonthlyPayments;
        $capacityTimeline = $capacityTimeline->map(function ($item) use (&$runningCapacity) {
            $runningCapacity += $item->freed_amount;
            $item->available_after = $runningCapacity;
            return $item;
        });

        return view('debts.index', compact(
            'group', 'debts', 'debtLimits',
            'budgetConfig', 'totalIncome', 'debtPercent', 'debtCapacity',
            'totalMonthlyPayments', 'availableCapacity', 'usedPercent',
            'capacityTimeline'
        ));
    }

    public function create(Group $group)
    {
        $accounts = $group->accounts()->active()->get();

        return view('debts.create', compact('group', 'accounts'));
    }

    public function store(Request $request, Group $group)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'type' => 'required|in:revolving_credit,no_interest_installments,personal_loan,mortgage,auto_loan,other',
            'account_id' => 'nullable|exists:accounts,id',
            'total_amount' => 'required|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'interest_rate' => 'nullable|numeric|min:0|max:100',
            'total_payments' => 'nullable|integer|min:0',
            'payment_amount' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'next_payment_date' => 'nullable|date',
            'cutoff_day' => 'nullable|integer|min:1|max:31',
            'payment_day' => 'nullable|integer|min:1|max:31',
            'notes' => 'nullable|string',
        ]);

        $validated['group_id'] = $group->id;
        $validated['paid_amount'] = $validated['paid_amount'] ?? 0;
        $validated['outstanding_balance'] = $validated['total_amount'] - $validated['paid_amount'];

        Debt::create($validated);

        return redirect()->route('debts.index', $group)
            ->with('success', 'Deuda registrada exitosamente.');
    }

    public function edit(Group $group, Debt $debt)
    {
        $accounts = $group->accounts()->active()->get();

        return view('debts.edit', compact('group', 'debt', 'accounts'));
    }

    public function update(Request $request, Group $group, Debt $debt)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'type' => 'required|in:revolving_credit,no_interest_installments,personal_loan,mortgage,auto_loan,other',
            'account_id' => 'nullable|exists:accounts,id',
            'total_amount' => 'required|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'interest_rate' => 'nullable|numeric|min:0|max:100',
            'total_payments' => 'nullable|integer|min:0',
            'payments_made' => 'nullable|integer|min:0',
            'payment_amount' => 'nullable|numeric|min:0',
            'next_payment_date' => 'nullable|date',
            'status' => 'required|in:active,paid_off,paused,overdue',
            'notes' => 'nullable|string',
        ]);

        $validated['outstanding_balance'] = $validated['total_amount'] - ($validated['paid_amount'] ?? 0);

        $debt->update($validated);

        return redirect()->route('debts.index', $group)
            ->with('success', 'Deuda actualizada exitosamente.');
    }

    public function destroy(Group $group, Debt $debt)
    {
        $debt->delete();

        return redirect()->route('debts.index', $group)
            ->with('success', 'Deuda eliminada exitosamente.');
    }
}
