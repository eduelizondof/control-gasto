<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800">{{ isset($category) ? 'Editar Categoría' : 'Nueva Categoría' }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                <form method="POST"
                    action="{{ isset($category) ? route('categories.update', [$group, $category]) : route('categories.store', $group) }}">
                    @csrf
                    @if(isset($category)) @method('PUT') @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="name" value="Nombre" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                :value="old('name', $category->name ?? '')" required placeholder="Ej: Restaurantes" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="type" value="Tipo" />
                            <select id="type" name="type"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required>
                                @foreach(['income' => 'Ingreso', 'expense' => 'Gasto', 'savings' => 'Ahorro', 'transfer' => 'Transferencia'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('type', $category->type ?? '') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label for="color" value="Color" />
                            <input id="color" name="color" type="color"
                                class="mt-1 block w-16 h-10 border-gray-300 rounded-md"
                                value="{{ old('color', $category->color ?? '#6366F1') }}">
                        </div>

                        <div>
                            <x-input-label for="parent_id" value="Categoría padre (opcional)" />
                            <select id="parent_id" name="parent_id"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Sin padre (categoría raíz)</option>
                                @foreach($parentCategories as $parent)
                                    <option value="{{ $parent->id }}" {{ old('parent_id', $category->parent_id ?? '') == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-8">
                        <a href="{{ route('categories.index', $group) }}"
                            class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium text-sm">Cancelar</a>
                        <x-primary-button>{{ isset($category) ? 'Actualizar' : 'Crear' }} Categoría</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>