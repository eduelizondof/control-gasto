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

    public function create()
    {
        return view('groups.create');
    }

    public function store(Request $request)
    {
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
        $group->delete();

        return redirect()->route('groups.index')
            ->with('success', 'Grupo eliminado exitosamente.');
    }
}
