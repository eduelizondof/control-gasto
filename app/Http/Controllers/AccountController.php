<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Group;
use App\Models\Transaction;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index(Group $group)
    {
        $accounts = $group->accounts()->orderBy('sort_order')->get();

        return view('accounts.index', compact('group', 'accounts'));
    }

    public function create(Group $group)
    {
        return view('accounts.create', compact('group'));
    }

    public function store(Request $request, Group $group)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:cash,debit,credit,investment,savings,emergency,fund',
            'bank' => 'nullable|string|max:100',
            'currency' => 'nullable|string|size:3',
            'initial_balance' => 'required|numeric',
            'credit_limit' => 'nullable|numeric|min:0',
            'cutoff_day' => 'nullable|integer|min:1|max:31',
            'payment_day' => 'nullable|integer|min:1|max:31',
            'color' => 'nullable|string|size:7',
            'icon' => 'nullable|string|max:50',
            'include_in_total' => 'boolean',
        ]);

        $validated['group_id'] = $group->id;
        $validated['current_balance'] = $validated['initial_balance'];
        $validated['include_in_total'] = $request->boolean('include_in_total', true);

        Account::create($validated);

        return redirect()->route('accounts.index', $group)
            ->with('success', 'Cuenta creada exitosamente.');
    }

    public function edit(Request $request, Group $group, Account $account)
    {
        $query = Transaction::where(function($q) use ($account) {
                $q->where('source_account_id', $account->id)
                  ->orWhere('destination_account_id', $account->id);
            });

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('description', 'like', $searchTerm)
                  ->orWhere('notes', 'like', $searchTerm)
                  ->orWhereHas('concept', function($cq) use ($searchTerm) {
                      $cq->where('name', 'like', $searchTerm);
                  });
            });
        }

        $transactions = $query->with(['category', 'concept', 'sourceAccount', 'destinationAccount'])
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $monthlyIncome = Transaction::where('destination_account_id', $account->id)
            ->where('type', 'income')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');

        $monthlyExpenses = Transaction::where('source_account_id', $account->id)
            ->where('type', 'expense')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');

        return view('accounts.edit', compact('group', 'account', 'transactions', 'monthlyIncome', 'monthlyExpenses'));
    }

    public function update(Request $request, Group $group, Account $account)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:cash,debit,credit,investment,savings,emergency,fund',
            'bank' => 'nullable|string|max:100',
            'credit_limit' => 'nullable|numeric|min:0',
            'cutoff_day' => 'nullable|integer|min:1|max:31',
            'payment_day' => 'nullable|integer|min:1|max:31',
            'color' => 'nullable|string|size:7',
            'icon' => 'nullable|string|max:50',
            'include_in_total' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['include_in_total'] = $request->boolean('include_in_total', true);
        $validated['is_active'] = $request->boolean('is_active', true);

        $account->update($validated);

        return redirect()->route('accounts.index', $group)
            ->with('success', 'Cuenta actualizada exitosamente.');
    }

    public function destroy(Group $group, Account $account)
    {
        $account->delete();

        return redirect()->route('accounts.index', $group)
            ->with('success', 'Cuenta eliminada exitosamente.');
    }
}
