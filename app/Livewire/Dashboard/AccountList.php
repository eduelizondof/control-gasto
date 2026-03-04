<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Group;

class AccountList extends Component
{
    public Group $group;

    public function render()
    {
        $accounts = $this->group->accounts()->active()->get();

        return view('livewire.dashboard.account-list', [
            'accounts' => $accounts,
        ]);
    }
}
