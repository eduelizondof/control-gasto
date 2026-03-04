<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class PendingInvitations extends Component
{
    public function render()
    {
        $user = auth()->user();
        $pendingInvitationsCount = $user->pendingInvitations()->count();

        return view('livewire.dashboard.pending-invitations', [
            'pendingInvitationsCount' => $pendingInvitationsCount,
        ]);
    }
}
