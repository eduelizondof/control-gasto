<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Group;

class ActiveDebts extends Component
{
    public Group $group;

    public function render()
    {
        $activeDebts = $this->group->debts()->active()->get();

        return view('livewire.dashboard.active-debts', [
            'activeDebts' => $activeDebts,
        ]);
    }
}
