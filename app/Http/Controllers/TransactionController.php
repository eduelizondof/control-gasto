<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct(
        private readonly TransactionService $transactionService
    ) {}

    public function index(Request $request, Group $group)
    {
        $query = $group->transactions()
            ->with(['category', 'sourceAccount', 'destinationAccount', 'concept', 'user'])
            ->confirmed();

        // Filters
        if ($request->filled('type')) {
            $query->ofType($request->type);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('account_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('source_account_id', $request->account_id)
                    ->orWhere('destination_account_id', $request->account_id);
            });
        }

        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        $transactions = $query->orderByDesc('date')
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $categories = $group->categories()->orderBy('name')->get();
        $accounts = $group->accounts()->active()->orderBy('name')->get();

        return view('transactions.index', compact('group', 'transactions', 'categories', 'accounts'));
    }

    public function create(Group $group)
    {
        $categories = $group->categories()->orderBy('type')->orderBy('name')->get();
        $accounts = $group->accounts()->active()->orderBy('name')->get();
        $concepts = $group->concepts()->orderBy('name')->get();

        return view('transactions.create', compact('group', 'categories', 'accounts', 'concepts'));
    }

    public function store(Request $request, Group $group)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'time' => 'nullable|date_format:H:i',
            'description' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'concept_id' => 'nullable|exists:concepts,id',
            'type' => 'required|in:income,expense,transfer,savings,adjustment',
            'source_account_id' => 'required|exists:accounts,id',
            'destination_account_id' => 'nullable|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string',
        ]);

        $validated['group_id'] = $group->id;
        $validated['user_id'] = $request->user()->id;
        $validated['status'] = 'confirmed';
        $validated['source'] = 'manual';

        $this->transactionService->create($validated);

        return redirect()->route('transactions.index', $group)
            ->with('success', 'Movimiento registrado exitosamente.');
    }

    public function edit(Group $group, Transaction $transaction)
    {
        $categories = $group->categories()->orderBy('type')->orderBy('name')->get();
        $accounts = $group->accounts()->active()->orderBy('name')->get();
        $concepts = $group->concepts()->orderBy('name')->get();

        return view('transactions.edit', compact('group', 'transaction', 'categories', 'accounts', 'concepts'));
    }

    public function update(Request $request, Group $group, Transaction $transaction)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'time' => 'nullable|date_format:H:i',
            'description' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'concept_id' => 'nullable|exists:concepts,id',
            'type' => 'required|in:income,expense,transfer,savings,adjustment',
            'source_account_id' => 'required|exists:accounts,id',
            'destination_account_id' => 'nullable|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string',
        ]);

        $this->transactionService->update($transaction, $validated);

        return redirect()->route('transactions.index', $group)
            ->with('success', 'Movimiento actualizado exitosamente.');
    }

    public function destroy(Group $group, Transaction $transaction)
    {
        $this->transactionService->delete($transaction);

        return redirect()->route('transactions.index', $group)
            ->with('success', 'Movimiento eliminado exitosamente.');
    }
}
