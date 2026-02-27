<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('concepts.index', $group) }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h2 class="text-2xl font-bold text-gray-800">Editar Concepto</h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <form method="POST" action="{{ route('concepts.update', [$group, $concept]) }}">
                    @csrf @method('PUT')

                    {{-- Nombre --}}
                    <div class="mb-5">
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">Nombre del
                            concepto</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $concept->name) }}" required
                            class="w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-400 text-sm py-2.5">
                        @error('name')
                            <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Categoría --}}
                    <div class="mb-5">
                        <label for="category_id"
                            class="block text-sm font-semibold text-gray-700 mb-1.5">Categoría</label>
                        <select name="category_id" id="category_id" required
                            class="w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-400 text-sm py-2.5">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $concept->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }} ({{ $category->type }})
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Descripción / Observaciones --}}
                    <div class="mb-6">
                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-1.5">Observaciones
                            <span class="text-gray-400 font-normal">(opcional)</span></label>
                        <textarea name="description" id="description" rows="3"
                            class="w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-400 text-sm py-2.5">{{ old('description', $concept->description) }}</textarea>
                        @error('description')
                            <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    @if($concept->is_system)
                        <div class="mb-6 bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-xl text-xs">
                            Este es un concepto del sistema. Puedes renombrarlo pero no eliminarlo.
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('concepts.index', $group) }}"
                            class="text-gray-500 text-sm font-medium hover:underline">Cancelar</a>
                        <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition shadow-sm">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>