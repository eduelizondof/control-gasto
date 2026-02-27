<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Catálogo de Conceptos</h2>
                <p class="text-sm text-gray-500 mt-1">Conceptos estándar para clasificar tus movimientos y presupuestos
                </p>
            </div>
            <a href="{{ route('concepts.create', $group) }}"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition shadow-sm text-center">+
                Nuevo Concepto</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @forelse($concepts as $categoryName => $categoryConceptsGroup)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-6">
                    {{-- Category Header --}}
                    <div class="px-5 sm:px-6 py-4 border-b border-gray-100 bg-gray-50/50 rounded-t-2xl">
                        <div class="flex items-center gap-2">
                            @php
                                $cat = $categoryConceptsGroup->first()->category;
                            @endphp
                            <div class="w-3 h-3 rounded-full" style="background-color: {{ $cat->color }}"></div>
                            <h3 class="text-base font-bold text-gray-800">{{ $categoryName }}</h3>
                            <span class="text-xs text-gray-400">({{ $categoryConceptsGroup->count() }})</span>
                        </div>
                    </div>

                    {{-- Concepts List --}}
                    <div class="divide-y divide-gray-50">
                        @foreach($categoryConceptsGroup as $concept)
                            <div class="flex items-center justify-between px-5 sm:px-6 py-3 hover:bg-gray-50/50 transition">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium text-gray-800 text-sm">{{ $concept->name }}</span>
                                        @if($concept->is_system)
                                            <span
                                                class="bg-blue-50 text-blue-600 text-[10px] font-semibold px-1.5 py-0.5 rounded">Sistema</span>
                                        @endif
                                    </div>
                                    @if($concept->description)
                                        <p class="text-xs text-gray-400 mt-0.5">{{ $concept->description }}</p>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2 ml-3 shrink-0">
                                    <a href="{{ route('concepts.edit', [$group, $concept]) }}"
                                        class="text-indigo-600 text-xs font-medium hover:underline">Editar</a>
                                    @unless($concept->is_system)
                                        <form method="POST" action="{{ route('concepts.destroy', [$group, $concept]) }}"
                                            onsubmit="return confirm('¿Eliminar este concepto?')">
                                            @csrf @method('DELETE')
                                            <button class="text-rose-500 text-xs font-medium hover:underline">Eliminar</button>
                                        </form>
                                    @endunless
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <p class="text-gray-500 mb-2">No hay conceptos creados</p>
                    <a href="{{ route('concepts.create', $group) }}"
                        class="text-indigo-600 font-medium hover:underline">Crear concepto →</a>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>