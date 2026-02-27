<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="text-2xl font-bold text-gray-800">Presupuesto Mensual</h2>
            <a href="{{ route('budgets.create', $group) }}"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition shadow-sm text-center">+
                Nuevo Presupuesto</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @forelse($budgets as $budget)
                @php
                    $totalMensual = $budget->items->where('is_active', true)->sum('monthly_amount');
                    $freqLabels = [
                        'monthly' => 'Mensual',
                        'bimonthly' => 'Bimestral',
                        'quarterly' => 'Trimestral',
                        'semiannual' => 'Semestral',
                        'annual' => 'Anual',
                    ];
                @endphp

                <div
                    class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-6 {{ $budget->is_active ? 'ring-2 ring-indigo-200' : '' }}">

                    {{-- Header del presupuesto --}}
                    <div class="p-5 sm:p-6 border-b border-gray-100">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="text-lg font-bold text-gray-800">{{ $budget->name }}</h3>
                                @if ($budget->is_active)
                                    <span
                                        class="bg-indigo-100 text-indigo-700 text-xs font-semibold px-2.5 py-1 rounded-lg">Activo</span>
                                @endif
                            </div>
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-2xl font-black text-gray-900">${{ number_format($totalMensual, 2) }}</span>
                                <span class="text-gray-400 text-sm">/mes</span>
                            </div>
                        </div>
                    </div>

                    {{-- Items del presupuesto --}}
                    @if ($budget->items->isNotEmpty())
                        {{-- Encabezado solo visible en desktop --}}
                        <div class="hidden lg:grid lg:grid-cols-12 gap-2 px-6 py-3 bg-gray-50 text-xs font-semibold text-gray-500 uppercase">
                            <div class="col-span-3">Concepto</div>
                            <div class="col-span-2">Categoría</div>
                            <div class="col-span-2 text-right">Monto</div>
                            <div class="col-span-2">Frecuencia</div>
                            <div class="col-span-2 text-right">Mensual</div>
                            <div class="col-span-1 text-center">Tipo</div>
                        </div>

                        <div class="divide-y divide-gray-50">
                            @foreach ($budget->items as $item)
                                @php
                                    $conceptName = $item->concept?->name ?? $item->custom_name ?? '-';
                                @endphp

                                {{-- Desktop row (lg+) --}}
                                <div class="hidden lg:grid lg:grid-cols-12 gap-2 items-center px-6 py-3 hover:bg-gray-50/50 text-sm">
                                    <div class="col-span-3 font-medium text-gray-800 truncate" title="{{ $conceptName }}">
                                        {{ $conceptName }}
                                    </div>
                                    <div class="col-span-2">
                                        <span class="inline-flex items-center gap-1 text-xs" style="color: {{ $item->category->color }}">
                                            <span class="w-2 h-2 rounded-full" style="background-color: {{ $item->category->color }}"></span>
                                            {{ $item->category->name }}
                                        </span>
                                    </div>
                                    <div class="col-span-2 text-right text-gray-600">${{ number_format($item->estimated_amount, 2) }}</div>
                                    <div class="col-span-2 text-gray-500">{{ $freqLabels[$item->frequency] ?? $item->frequency }}</div>
                                    <div class="col-span-2 text-right font-bold text-gray-800">${{ number_format($item->monthly_amount, 2) }}</div>
                                    <div class="col-span-1 text-center">
                                        <span class="px-2 py-0.5 rounded text-xs {{ $item->is_fixed ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700' }}">
                                            {{ $item->is_fixed ? 'Fijo' : 'Variable' }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Mobile / Tablet card (< lg) --}}
                                <div class="lg:hidden p-4 hover:bg-gray-50/50">
                                    <div class="flex items-start justify-between gap-3 mb-2">
                                        <div class="min-w-0 flex-1">
                                            <p class="font-semibold text-gray-800 text-sm truncate" title="{{ $conceptName }}">
                                                {{ $conceptName }}
                                            </p>
                                            <span class="inline-flex items-center gap-1 text-xs mt-0.5" style="color: {{ $item->category->color }}">
                                                <span class="w-2 h-2 rounded-full" style="background-color: {{ $item->category->color }}"></span>
                                                {{ $item->category->name }}
                                            </span>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <p class="font-bold text-gray-900 text-sm">${{ number_format($item->monthly_amount, 2) }}</p>
                                            <p class="text-xs text-gray-400">/mes</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 flex-wrap text-xs text-gray-500">
                                        @if ($item->estimated_amount != $item->monthly_amount)
                                            <span class="bg-gray-100 px-2 py-0.5 rounded">${{ number_format($item->estimated_amount, 2) }}
                                                · {{ $freqLabels[$item->frequency] ?? $item->frequency }}</span>
                                        @else
                                            <span class="bg-gray-100 px-2 py-0.5 rounded">{{ $freqLabels[$item->frequency] ?? $item->frequency }}</span>
                                        @endif
                                        <span class="px-2 py-0.5 rounded {{ $item->is_fixed ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700' }}">
                                            {{ $item->is_fixed ? 'Fijo' : 'Variable' }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Total footer --}}
                        <div class="px-5 sm:px-6 py-3 bg-gray-50/60 border-t border-gray-100">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-semibold text-gray-600">Total mensual</span>
                                <span class="text-base font-black text-gray-900">${{ number_format($totalMensual, 2) }}</span>
                            </div>
                        </div>
                    @endif

                    {{-- Acciones --}}
                    <div class="p-4 border-t border-gray-100 flex items-center gap-3">
                        <a href="{{ route('budgets.edit', [$group, $budget]) }}"
                            class="text-indigo-600 text-sm font-medium hover:underline">Editar</a>
                        <span class="text-gray-300">·</span>
                        <form method="POST" action="{{ route('budgets.destroy', [$group, $budget]) }}"
                            onsubmit="return confirm('¿Eliminar este presupuesto y todos sus items?')">
                            @csrf @method('DELETE')
                            <button class="text-rose-500 text-sm font-medium hover:underline">Eliminar</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <p class="text-gray-500 mb-2">No hay presupuestos creados</p>
                    <a href="{{ route('budgets.create', $group) }}"
                        class="text-indigo-600 font-medium hover:underline">Crear presupuesto →</a>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>