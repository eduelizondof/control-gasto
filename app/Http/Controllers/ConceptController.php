<?php

namespace App\Http\Controllers;

use App\Models\Concept;
use App\Models\Group;
use Illuminate\Http\Request;

class ConceptController extends Controller
{
    public function index(Group $group)
    {
        $concepts = $group->concepts()
            ->with('category')
            ->orderBy('name')
            ->get()
            ->groupBy(fn($c) => $c->category->name);

        return view('concepts.index', compact('group', 'concepts'));
    }

    public function create(Group $group)
    {
        $categories = $group->categories()->orderBy('type')->orderBy('name')->get();

        return view('concepts.create', compact('group', 'categories'));
    }

    public function store(Request $request, Group $group)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string|max:500',
        ]);

        $group->concepts()->create([
            'name' => $validated['name'],
            'category_id' => $validated['category_id'],
            'description' => $validated['description'] ?? null,
            'is_system' => false,
        ]);

        return redirect()->route('concepts.index', $group)
            ->with('success', 'Concepto creado exitosamente.');
    }

    public function edit(Group $group, Concept $concept)
    {
        $categories = $group->categories()->orderBy('type')->orderBy('name')->get();

        return view('concepts.edit', compact('group', 'concept', 'categories'));
    }

    public function update(Request $request, Group $group, Concept $concept)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string|max:500',
        ]);

        $concept->update([
            'name' => $validated['name'],
            'category_id' => $validated['category_id'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('concepts.index', $group)
            ->with('success', 'Concepto actualizado exitosamente.');
    }

    public function destroy(Group $group, Concept $concept)
    {
        if ($concept->is_system) {
            return redirect()->route('concepts.index', $group)
                ->with('error', 'Los conceptos del sistema no se pueden eliminar.');
        }

        $concept->delete();

        return redirect()->route('concepts.index', $group)
            ->with('success', 'Concepto eliminado exitosamente.');
    }
}
