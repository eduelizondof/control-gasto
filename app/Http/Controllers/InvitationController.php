<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;

class InvitationController extends Controller
{
    /**
     * Show the invite form for a specific group.
     */
    public function create(Group $group)
    {
        // Ensure the current user is a member of this group
        $this->authorizeGroupMember($group);

        return view('groups.invite', compact('group'));
    }

    /**
     * Process the invitation.
     */
    public function store(Request $request, Group $group)
    {
        $this->authorizeGroupMember($group);

        $validated = $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $email = $validated['email'];
        $inviter = $request->user();

        // Can't invite yourself
        if ($email === $inviter->email) {
            return back()->withErrors(['email' => 'No puedes invitarte a ti mismo.'])->withInput();
        }

        $invitee = User::where('email', $email)->first();

        if (!$invitee) {
            // User doesn't exist → Generic message
            return redirect()->route('groups.invite', $group)
                ->with('invite_processed', true);
        }

        // Check if already in the group (any status)
        $existing = $group->allUsers()->where('users.id', $invitee->id)->first();

        if ($existing) {
            $status = $existing->pivot->status;
            // If active or pending, we do nothing but return the generic message
            if (in_array($status, ['active', 'pending'])) {
                return redirect()->route('groups.invite', $group)
                     ->with('invite_processed', true);
            }
            // If rejected, allow re-invite by setting to pending behind the scenes
            $group->allUsers()->updateExistingPivot($invitee->id, [
                'status' => 'pending',
                'is_active' => false,
                'invited_by' => $inviter->id,
            ]);

            return redirect()->route('groups.invite', $group)
                ->with('invite_processed', true);
        }

        // Attach with pending status
        $group->allUsers()->attach($invitee->id, [
            'role' => 'member',
            'invited_by' => $inviter->id,
            'joined_at' => now(),
            'is_active' => false,
            'status' => 'pending',
        ]);

        return redirect()->route('groups.invite', $group)
            ->with('invite_processed', true);
    }

    /**
     * Show all pending invitations for the authenticated user.
     */
    public function index(Request $request)
    {
        $invitations = $request->user()
            ->pendingInvitations()
            ->with('creator')
            ->get();

        return view('invitations.index', compact('invitations'));
    }

    /**
     * Accept an invitation.
     */
    public function accept(Request $request, Group $group)
    {
        $user = $request->user();

        $pivot = $user->pendingInvitations()->where('groups.id', $group->id)->first();

        if (!$pivot) {
            return redirect()->route('invitations.index')
                ->withErrors(['error' => 'No se encontró la invitación.']);
        }

        $user->pendingInvitations()->updateExistingPivot($group->id, [
            'status' => 'active',
            'is_active' => true,
            'joined_at' => now(),
        ]);

        return redirect()->route('dashboard')
            ->with('success', "¡Te uniste al grupo \"{$group->name}\"!");
    }

    /**
     * Reject an invitation.
     */
    public function reject(Request $request, Group $group)
    {
        $user = $request->user();

        $pivot = $user->pendingInvitations()->where('groups.id', $group->id)->first();

        if (!$pivot) {
            return redirect()->route('invitations.index')
                ->withErrors(['error' => 'No se encontró la invitación.']);
        }

        $user->pendingInvitations()->updateExistingPivot($group->id, [
            'status' => 'rejected',
        ]);

        return redirect()->route('invitations.index')
            ->with('success', 'Invitación rechazada.');
    }

    /**
     * Ensure the authenticated user belongs to the group.
     */
    private function authorizeGroupMember(Group $group): void
    {
        $user = request()->user();
        if (!$user->groups()->where('groups.id', $group->id)->exists()) {
            abort(403, 'No tienes acceso a este grupo.');
        }
    }
}
