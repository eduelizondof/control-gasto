<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800">Categorías</h2>
            <a href="{{ route('categories.create', $group) }}"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition shadow-sm">+
                Nueva Categoría</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @php $typeLabels = ['income' => 'Ingresos', 'expense' => 'Gastos', 'savings' => 'Ahorro', 'transfer' => 'Transferencias']; @endphp
            @foreach(['income', 'expense', 'savings', 'transfer'] as $type)
                @php $typeCats = $categories->where('type', $type); @endphp
                @if($typeCats->isNotEmpty())
                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-gray-700 mb-4">{{ $typeLabels[$type] }}</h3>
                        <div class="grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                            @foreach($typeCats as $category)
                                <div
                                    class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:border-indigo-200 transition">
                                    <div class="flex items-center gap-3 mb-2">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                                            style="background-color: {{ $category->color }}20">
                                            <div class="w-4 h-4 rounded-full" style="background-color: {{ $category->color }}">
                                            </div>
                                        </div>
                                        <div>
                                            <span class="font-semibold text-gray-800 text-sm">{{ $category->name }}</span>
                                            @if($category->is_system)
                                                <span class="text-xs text-gray-400 ml-1">· Sistema</span>
                                            @endif
                                        </div>
                                    </div>
                                    @if($category->children->isNotEmpty())
                                        <div class="ml-13 mt-2 space-y-1">
                                            @foreach($category->children as $child)
                                                <div class="text-xs text-gray-500 flex items-center gap-1">
                                                    <span class="w-1.5 h-1.5 rounded-full"
                                                        style="background-color: {{ $child->color }}"></span>
                                                    {{ $child->name }}
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                    <div class="flex items-center gap-2 mt-3 pt-2 border-t border-gray-50">
                                        <a href="{{ route('categories.edit', [$group, $category]) }}"
                                            class="text-indigo-600 text-xs font-medium hover:underline">Editar</a>
                                        @if(!$category->is_system)
                                            <span class="text-gray-300">·</span>
                                            <form method="POST" action="{{ route('categories.destroy', [$group, $category]) }}"
                                                data-confirm="Se eliminará la categoría '{{ $category->name }}'. Los movimientos asociados quedarán sin categoría."
                                                data-title="¿Eliminar categoría?" data-btn-text="Sí, eliminar">
                                                @csrf @method('DELETE')
                                                <button class="text-rose-500 text-xs font-medium hover:underline">Eliminar</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</x-app-layout>