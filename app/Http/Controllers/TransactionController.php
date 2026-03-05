<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct(
        private readonly TransactionService $transactionService,
        private readonly \App\Services\NotificationService $notificationService
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

        // Apply date filters only if NOT searching (to search in all history)
        if (!$request->filled('search')) {
            if ($request->filled('date_from')) {
                $query->where('date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->where('date', '<=', $request->date_to);
            }
        }

        // Default to today if no date filter or search is present
        if (!$request->filled('date_from') && !$request->filled('date_to') && !$request->filled('search')) {
            $query->whereDate('date', today());
            $request->merge([
                'date_from' => today()->toDateString(),
                'date_to' => today()->toDateString(),
            ]);
        }

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

        $transactions = $query->orderByDesc('date')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $categories = $group->categories()->orderBy('name')->get();
        $accounts = $group->accounts()->active()->orderBy('name')->get();

        return view('transactions.index', compact('group', 'transactions', 'categories', 'accounts'));
    }

    public function create(Request $request, Group $group)
    {
        $transaction = null;
        if ($request->filled('duplicate_id')) {
            $sourceTxn = $group->transactions()->find($request->duplicate_id);
            if ($sourceTxn) {
                $transaction = $sourceTxn->replicate();
                $transaction->date = now();
                $transaction->time = now()->format('H:i');
                $transaction->receipt_path = null;
            }
        }

        if ($request->filled('payment_calendar_id')) {
            $calendar = \App\Models\PaymentCalendar::where('group_id', $group->id)->find($request->payment_calendar_id);
            if ($calendar) {
                $transaction = new Transaction([
                    'type' => 'income',
                    'amount' => $calendar->amount,
                    'description' => $calendar->concept,
                    'category_id' => $calendar->category_id,
                    'concept_id' => $calendar->concept_id,
                    'source_account_id' => $calendar->account_id,
                    'date' => $calendar->payment_date,
                    'time' => now()->format('H:i'),
                ]);
            }
        }

        $categories = $group->categories()->orderBy('type')->orderBy('name')->get();
        $accounts = $group->accounts()->active()->orderBy('name')->get();
        $concepts = $group->concepts()->orderBy('name')->get();

        return view('transactions.create', compact('group', 'categories', 'accounts', 'concepts', 'transaction'));
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
            'receipt' => 'nullable|file|mimes:jpeg,png,jpg,pdf,doc,docx|max:5120',
        ]);

        if ($request->hasFile('receipt')) {
            $validated['receipt_path'] = $request->file('receipt')->store('receipts', 'public');
        }

        $validated['group_id'] = $group->id;
        $validated['user_id'] = $request->user()->id;
        $validated['status'] = 'confirmed';
        $validated['source'] = 'manual';

        $transaction = $this->transactionService->create($validated);

        if ($request->filled('payment_calendar_id')) {
            $calendar = \App\Models\PaymentCalendar::where('group_id', $group->id)->find($request->payment_calendar_id);
            if ($calendar) {
                $calendar->update(['transaction_id' => $transaction->id]);
            }
        }

        // Define friendly name for notification
        $typeLabel = match ($validated['type']) {
            'income' => 'Ingreso',
            'expense' => 'Gasto',
            'transfer' => 'Transferencia',
            'savings' => 'Ahorro',
            'adjustment' => 'Ajuste',
            default => 'Movimiento',
        };

        $formattedAmount = number_format($validated['amount'], 2);

        $this->notificationService->notifyGroup(
            $group,
            $request->user(),
            "Nuevo $typeLabel registrado",
            "{$request->user()->name} registró un $typeLabel por $$formattedAmount.",
            'success'
        );

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
            'receipt' => 'nullable|file|mimes:jpeg,png,jpg,pdf,doc,docx|max:5120',
        ]);

        if ($request->hasFile('receipt')) {
            if ($transaction->receipt_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($transaction->receipt_path);
            }
            $validated['receipt_path'] = $request->file('receipt')->store('receipts', 'public');
        }

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
    public function show(Group $group, Transaction $transaction)
    {
        // Ensure the transaction belongs to the group
        if ($transaction->group_id !== $group->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $transaction->load(['category', 'sourceAccount', 'destinationAccount', 'concept']);

        return response()->json([
            'id' => $transaction->id,
            'description' => $transaction->description ?: ($transaction->concept?->name ?: $transaction->category->name),
            'amount' => number_format((float) $transaction->amount, 2),
            'type' => $transaction->type,
            'typeLabel' => match($transaction->type) { 
                'income' => 'Ingreso', 
                'expense' => 'Gasto', 
                'transfer' => 'Transferencia', 
                'savings' => 'Ahorro', 
                'adjustment' => 'Ajuste', 
                default => 'Otro' 
            },
            'date' => $transaction->date ? \Carbon\Carbon::parse($transaction->date)->format('d/m/Y') : '',
            'categoryName' => $transaction->category->name,
            'categoryColor' => $transaction->category->color,
            'categoryIcon' => $transaction->category->icon,
            'categoryIconUrl' => ($transaction->category->icon && str_contains($transaction->category->icon, '/')) ? \Illuminate\Support\Facades\Storage::url($transaction->category->icon) : null,
            'sourceAccount' => $transaction->sourceAccount->name,
            'destinationAccount' => $transaction->destinationAccount?->name ?? '',
            'receipt' => $transaction->receipt_path ? \Illuminate\Support\Facades\Storage::url($transaction->receipt_path) : '',
            'receiptIsImage' => $transaction->receipt_path && preg_match('/\.(jpg|jpeg|png)$/i', $transaction->receipt_path) ? true : false,
            'notes' => $transaction->notes ?? '',
            'editUrl' => route('transactions.edit', [$group, $transaction]),
            'duplicateUrl' => route('transactions.create', [$group, 'duplicate_id' => $transaction->id])
        ]);
    }
}
