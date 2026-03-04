<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $group = $user->groups()->first();
        $pendingInvitationsCount = $user->pendingInvitations()->count();

        if (!$group) {
            if ($pendingInvitationsCount > 0) {
                return redirect()->route('invitations.index')
                    ->with('info', 'Tienes invitaciones pendientes. ¡Acepta una para empezar o crea tu propio grupo!');
            }

            return redirect()->route('groups.create')
                ->with('info', 'Crea tu primer grupo para empezar.');
        }

        return view('dashboard', compact('group'));
    }
}
