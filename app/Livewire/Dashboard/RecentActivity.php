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

        return view('livewire.dashboard.recent-activity', [
            'recentTransactions' => $recentTransactions,
        ]);
    }
}
