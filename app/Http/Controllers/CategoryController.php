<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Group;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Group $group)
    {
        $categories = $group->categories()
            ->roots()
            ->with('children')
            ->orderBy('type')
            ->orderBy('sort_order')
            ->get();

        return view('categories.index', compact('group', 'categories'));
    }

    public function create(Group $group)
    {
        $parentCategories = $group->categories()->roots()->get();

        return view('categories.create', compact('group', 'parentCategories'));
    }

    public function store(Request $request, Group $group)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:income,expense,savings,transfer',
            'color' => 'nullable|string|size:7',
            'icon' => 'nullable|string|max:50',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        $validated['group_id'] = $group->id;

        Category::create($validated);

        return redirect()->route('categories.index', $group)
            ->with('success', 'Categoría creada exitosamente.');
    }

    public function edit(Group $group, Category $category)
    {
        $parentCategories = $group->categories()->roots()->where('id', '!=', $category->id)->get();

        return view('categories.edit', compact('group', 'category', 'parentCategories'));
    }

    public function update(Request $request, Group $group, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:income,expense,savings,transfer',
            'color' => 'nullable|string|size:7',
            'icon' => 'nullable|string|max:50',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index', $group)
            ->with('success', 'Categoría actualizada exitosamente.');
    }

    public function destroy(Group $group, Category $category)
    {
        $category->delete();

        return redirect()->route('categories.index', $group)
            ->with('success', 'Categoría eliminada exitosamente.');
    }
}
