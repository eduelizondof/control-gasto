<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Group;

class QuincenaAnalysis extends Component
{
    public Group $group;

    public function render()
    {
        $thisMonthTransactions = $this->group->transactions()
            ->confirmed()
            ->thisMonth()
            ->get();

        $remindersThisMonth = $this->group->reminders()
            ->active()
            ->whereNotNull('next_date')
            ->whereYear('next_date', now()->year)
            ->whereMonth('next_date', now()->month)
            ->get();

        $q1Load = 0;
        $q1Paid = 0;
        $q2Load = 0;
        $q2Paid = 0;

        foreach ($remindersThisMonth as $r) {
            $isPaid = false;
            if ($r->category_id && $r->concept_id) {
                $isPaid = $thisMonthTransactions->some(fn($t) => $t->category_id == $r->category_id && $t->concept_id == $r->concept_id);
            } elseif ($r->concept_id) {
                $isPaid = $thisMonthTransactions->some(fn($t) => $t->concept_id == $r->concept_id);
            } elseif ($r->category_id) {
                $isPaid = $thisMonthTransactions->some(fn($t) => $t->category_id == $r->category_id);
            }

            $amount = (float) $r->estimated_amount;
            $day = $r->next_date->day;

            if ($day <= 15) {
                $q1Load += $amount;
                if ($isPaid) $q1Paid += $amount;
            } else {
                $q2Load += $amount;
                if ($isPaid) $q2Paid += $amount;
            }
        }

        $q1Pending = max(0, $q1Load - $q1Paid);
        $q2Pending = max(0, $q2Load - $q2Paid);

        return view('livewire.dashboard.quincena-analysis', [
            'q1Load' => $q1Load,
            'q2Load' => $q2Load,
            'q1Pending' => $q1Pending,
            'q2Pending' => $q2Pending,
        ]);
    }
}
