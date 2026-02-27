<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800">Editar Grupo</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                <form method="POST" action="{{ route('groups.update', $group) }}">
                    @csrf @method('PUT')
                    <div class="mb-6">
                        <x-input-label for="name" value="Nombre del grupo" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $group->name)" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div class="mb-6">
                        <x-input-label for="description" value="Descripción (opcional)" />
                        <textarea id="description" name="description" rows="3"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $group->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('groups.index') }}"
                            class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium text-sm">Cancelar</a>
                        <x-primary-button>Actualizar Grupo</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>