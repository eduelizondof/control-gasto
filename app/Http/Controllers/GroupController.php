<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index(Request $request)
    {
        $groups = $request->user()->groups()->withCount('users')->get();

        return view('groups.index', compact('groups'));
    }

    public function create(Request $request)
    {
        if ($request->user()->currentGroup()) {
            return redirect()->route('groups.index')
                ->withErrors(['error' => 'Ya perteneces a un grupo activo. No puedes crear otro.']);
        }

        return view('groups.create');
    }

    public function store(Request $request)
    {
        if ($request->user()->currentGroup()) {
            return redirect()->back()
                ->withErrors(['name' => 'Ya perteneces a un grupo activo. No puedes crear otro.'])
                ->withInput();
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        $group = Group::create([
            ...$validated,
            'created_by' => $request->user()->id,
        ]);

        $group->users()->attach($request->user()->id, [
            'role' => 'admin',
            'joined_at' => now(),
            'is_active' => true,
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Grupo creado exitosamente.');
    }

    public function edit(Group $group)
    {
        return view('groups.edit', compact('group'));
    }

    public function update(Request $request, Group $group)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        $group->update($validated);

        return redirect()->route('groups.index')
            ->with('success', 'Grupo actualizado exitosamente.');
    }

    public function destroy(Group $group)
    {
        if ($group->created_by !== request()->user()->id) {
            return redirect()->route('groups.index')
                ->withErrors(['error' => 'Solo el creador del grupo puede eliminarlo.']);
        }

        $group->delete();

        return redirect()->route('groups.index')
            ->with('success', 'Grupo eliminado exitosamente.');
    }

    public function leave(Request $request, Group $group)
    {
        if ($group->created_by === $request->user()->id) {
            return redirect()->route('groups.index')
                ->withErrors(['error' => 'Eres el creador del grupo. Si deseas cancelarlo, debes eliminarlo.']);
        }

        if (!$group->users()->where('users.id', $request->user()->id)->exists()) {
            return redirect()->route('groups.index')
                ->withErrors(['error' => 'No perteneces a este grupo.']);
        }

        // Mark as inactive for this user
        $group->users()->updateExistingPivot($request->user()->id, [
            'is_active' => false,
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Has salido del grupo exitosamente.');
    }
}
