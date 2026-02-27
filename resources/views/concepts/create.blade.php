<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('concepts.index', $group) }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h2 class="text-2xl font-bold text-gray-800">Nuevo Concepto</h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <form method="POST" action="{{ route('concepts.store', $group) }}">
                    @csrf

                    {{-- Nombre --}}
                    <div class="mb-5">
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">Nombre del
                            concepto</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            placeholder="Ej: Luz, Agua, Mantenimiento..."
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
                            <option value="">Selecciona una categoría</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                            placeholder="Notas adicionales sobre este concepto..."
                            class="w-full rounded-xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-400 text-sm py-2.5">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('concepts.index', $group) }}"
                            class="text-gray-500 text-sm font-medium hover:underline">Cancelar</a>
                        <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition shadow-sm">
                            Crear Concepto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>