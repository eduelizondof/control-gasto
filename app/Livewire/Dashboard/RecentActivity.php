<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Group;

class RecentActivity extends Component
{
    public Group $group;

    public function render()
    {
        $recentTransactions = $this->group->transactions()
            ->with(['category', 'sourceAccount', 'destinationAccount', 'concept'])
            ->confirmed()
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

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

        $upcomingReminders = $upcomingRemindersRaw->filter(function ($reminder) use ($thisMonthTransactions) {
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

        return view('livewire.dashboard.recent-activity', [
            'recentTransactions' => $recentTransactions,
            'upcomingReminders' => $upcomingReminders,
        ]);
    }
}
