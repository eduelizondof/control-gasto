<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Group;

class UpcomingBigPayments extends Component
{
    public Group $group;

    public function render()
    {
        $bigPayments = $this->group->reminders()
            ->active()
            ->whereIn('frequency', ['annual', 'semiannual'])
            ->whereNotNull('next_date')
            ->orderBy('next_date')
            ->take(5)
            ->get();

        $currentMonth = now()->month;
        $bonuses = $this->group->expectedBonuses()
            ->where('is_active', true)
            ->orderByRaw("CASE WHEN month >= {$currentMonth} THEN 0 ELSE 1 END")
            ->orderBy('month')
            ->orderBy('day')
            ->take(5)
            ->get();

        return view('livewire.dashboard.upcoming-big-payments', [
            'bigPayments' => $bigPayments,
            'bonuses' => $bonuses,
        ]);
    }
}
