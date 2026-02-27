<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800">Mis Grupos</h2>
            <a href="{{ route('groups.create') }}"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition shadow-sm">
                + Nuevo Grupo
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($groups as $group)
                    <div
                        class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:border-indigo-200 transition">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800">{{ $group->name }}</h3>
                                <p class="text-gray-400 text-sm">{{ $group->users_count }}
                                    miembro{{ $group->users_count > 1 ? 's' : '' }}</p>
                            </div>
                        </div>
                        @if($group->description)
                            <p class="text-gray-500 text-sm mb-4">{{ $group->description }}</p>
                        @endif
                        <div class="flex items-center gap-2">
                            <a href="{{ route('groups.edit', $group) }}"
                                class="text-indigo-600 text-sm font-medium hover:underline">Editar</a>
                            <span class="text-gray-300">·</span>
                            <form method="POST" action="{{ route('groups.destroy', $group) }}"
                                onsubmit="return confirm('¿Seguro que deseas eliminar este grupo?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="text-rose-500 text-sm font-medium hover:underline">Eliminar</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                </path>
                            </svg>
                        </div>
                        <p class="text-gray-500 mb-2">No tienes grupos aún</p>
                        <a href="{{ route('groups.create') }}" class="text-indigo-600 font-medium hover:underline">Crear tu
                            primer grupo →</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>