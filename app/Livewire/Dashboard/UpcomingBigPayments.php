<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Group;

class UpcomingBigPayments extends Component
{
    public Group $group;

    public function render()
    {
        $upcomingRemindersRaw = $this->group->reminders()
            ->active()
            ->with(['category', 'concept'])
            ->whereNotNull('next_date')
            ->orderBy('next_date')
            ->get();

        $thisMonthTransactions = $this->group->transactions()
            ->confirmed()
            ->thisMonth()
            ->get();

        $payments = $upcomingRemindersRaw->filter(function ($reminder) use ($thisMonthTransactions) {
            if ($reminder->category_id && $reminder->concept_id) {
                return !$thisMonthTransactions->some(fn($t) => $t->category_id == $reminder->category_id && $t->concept_id == $reminder->concept_id);
            }
            if ($reminder->concept_id) {
                return !$thisMonthTransactions->some(fn($t) => $t->concept_id == $reminder->concept_id);
            }
            if ($reminder->category_id) {
                return !$thisMonthTransactions->some(fn($t) => $t->category_id == $reminder->category_id);
            }
            return true;
        })->take(5);

        $currentMonth = now()->month;
        $bonuses = $this->group->expectedBonuses()
            ->where('is_active', true)
            ->orderByRaw("CASE WHEN month >= {$currentMonth} THEN 0 ELSE 1 END")
            ->orderBy('month')
            ->orderBy('day')
            ->take(5)
            ->get();

        return view('livewire.dashboard.upcoming-big-payments', [
            'payments' => $payments,
            'bonuses' => $bonuses,
        ]);
    }
}
