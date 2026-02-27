<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Reminder;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function index(Group $group)
    {
        $reminders = $group->reminders()
            ->with(['account', 'concept'])
            ->orderBy('next_date')
            ->get();

        return view('reminders.index', compact('group', 'reminders'));
    }

    public function create(Group $group)
    {
        $accounts = $group->accounts()->active()->get();
        $concepts = $group->concepts()->orderBy('name')->get();
        $debts = $group->debts()->active()->get();

        return view('reminders.create', compact('group', 'accounts', 'concepts', 'debts'));
    }

    public function store(Request $request, Group $group)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'type' => 'required|in:fixed_payment,card_cutoff,annuity,expiration,debt,custom',
            'account_id' => 'nullable|exists:accounts,id',
            'debt_id' => 'nullable|exists:debts,id',
            'concept_id' => 'nullable|exists:concepts,id',
            'estimated_amount' => 'nullable|numeric|min:0',
            'frequency' => 'required|in:one_time,weekly,biweekly,monthly,bimonthly,quarterly,semiannual,annual',
            'day_of_month' => 'nullable|integer|min:1|max:31',
            'specific_date' => 'nullable|date',
            'advance_days' => 'nullable|integer|min:0|max:30',
            'auto_create_transaction' => 'boolean',
        ]);

        $validated['group_id'] = $group->id;
        $validated['auto_create_transaction'] = $request->boolean('auto_create_transaction');
        $validated['next_date'] = $this->calculateNextDate($validated);

        Reminder::create($validated);

        return redirect()->route('reminders.index', $group)
            ->with('success', 'Recordatorio creado exitosamente.');
    }

    public function edit(Group $group, Reminder $reminder)
    {
        $accounts = $group->accounts()->active()->get();
        $concepts = $group->concepts()->orderBy('name')->get();
        $debts = $group->debts()->active()->get();

        return view('reminders.edit', compact('group', 'reminder', 'accounts', 'concepts', 'debts'));
    }

    public function update(Request $request, Group $group, Reminder $reminder)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'type' => 'required|in:fixed_payment,card_cutoff,annuity,expiration,debt,custom',
            'account_id' => 'nullable|exists:accounts,id',
            'debt_id' => 'nullable|exists:debts,id',
            'concept_id' => 'nullable|exists:concepts,id',
            'estimated_amount' => 'nullable|numeric|min:0',
            'frequency' => 'required|in:one_time,weekly,biweekly,monthly,bimonthly,quarterly,semiannual,annual',
            'day_of_month' => 'nullable|integer|min:1|max:31',
            'specific_date' => 'nullable|date',
            'advance_days' => 'nullable|integer|min:0|max:30',
            'auto_create_transaction' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['auto_create_transaction'] = $request->boolean('auto_create_transaction');
        $validated['is_active'] = $request->boolean('is_active', true);

        $reminder->update($validated);

        return redirect()->route('reminders.index', $group)
            ->with('success', 'Recordatorio actualizado exitosamente.');
    }

    public function destroy(Group $group, Reminder $reminder)
    {
        $reminder->delete();

        return redirect()->route('reminders.index', $group)
            ->with('success', 'Recordatorio eliminado exitosamente.');
    }

    private function calculateNextDate(array $data): ?string
    {
        if (!empty($data['specific_date'])) {
            return $data['specific_date'];
        }

        if (!empty($data['day_of_month'])) {
            $day = min((int) $data['day_of_month'], 28);
            $next = now()->day($day);

            if ($next->isPast()) {
                $next->addMonth();
            }

            return $next->format('Y-m-d');
        }

        return null;
    }
}
