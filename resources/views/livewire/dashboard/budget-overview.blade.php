<div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
    <h3 class="text-lg font-bold text-gray-800 mb-4">Presupuesto vs Gasto Real</h3>
    @if($activeBudget)
        @php
            $budgetDiff = $budgetTotal - $budgetSpent;
            $budgetPercent = $budgetTotal > 0 ? min(100, round(($budgetSpent / $budgetTotal) * 100)) : 0;
        @endphp
        {{-- Global bar --}}
        <div class="mb-5">
            <div class="flex justify-between text-sm mb-2">
                <span class="text-gray-600">Gastado: ${{ number_format($budgetSpent, 2) }}</span>
                <span class="text-gray-600">de ${{ number_format($budgetTotal, 2) }}</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-4 overflow-hidden">
                <div class="h-4 rounded-full transition-all duration-500 {{ $budgetPercent > 90 ? 'bg-rose-500' : ($budgetPercent > 70 ? 'bg-amber-500' : 'bg-emerald-500') }}"
                    style="width: {{ $budgetPercent }}%"></div>
            </div>
            <p class="text-sm mt-3 {{ $budgetDiff >= 0 ? 'text-emerald-600' : 'text-rose-600' }} font-semibold">
                {{ $budgetDiff >= 0 ? 'Disponible: $' . number_format($budgetDiff, 2) : 'Excedido: $' . number_format(abs($budgetDiff), 2) }}
            </p>
        </div>

        {{-- Per-concept breakdown --}}
        @if($budgetBreakdown->isNotEmpty())
            <div class="border-t border-gray-100 pt-4">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Desglose por concepto</p>
                <div class="space-y-3 max-h-64 overflow-y-auto pr-1">
                    @foreach($budgetBreakdown as $entry)
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <div class="flex items-center gap-1.5 min-w-0">
                                    <span class="w-2 h-2 rounded-full shrink-0"
                                        style="background-color: {{ $entry->category->color }}"></span>
                                    <span class="font-medium text-gray-700 truncate">{{ $entry->name }}</span>
                                </div>
                                <span
                                    class="font-bold shrink-0 ml-2 {{ $entry->diff >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $entry->diff >= 0 ? '-$' . number_format($entry->diff, 2) : '+$' . number_format(abs($entry->diff), 2) }}
                                </span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                <div class="h-1.5 rounded-full transition-all duration-300 {{ $entry->percent > 90 ? 'bg-rose-400' : ($entry->percent > 70 ? 'bg-amber-400' : 'bg-emerald-400') }}"
                                    style="width: {{ $entry->percent }}%"></div>
                            </div>
                            <div class="flex justify-between text-[10px] text-gray-400 mt-0.5">
                                <span>${{ number_format($entry->spent, 2) }} gastado</span>
                                <span>${{ number_format($entry->budgeted, 2) }} presup.</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Out of budget breakdown --}}
        @if($outOfBudgetBreakdown->isNotEmpty())
            <div class="border-t border-rose-100 mt-5 pt-4">
                <div class="flex justify-between items-center mb-3">
                    <p class="text-xs font-bold text-rose-500 uppercase tracking-wider">Fuera de Presupuesto</p>
                    <span class="text-xs font-bold text-rose-600">${{ number_format($outOfBudgetTotal, 2) }}</span>
                </div>
                <div class="space-y-3 max-h-48 overflow-y-auto pr-1">
                    @foreach($outOfBudgetBreakdown as $entry)
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <div class="flex items-center gap-1.5 min-w-0">
                                    <span class="w-2 h-2 rounded-full shrink-0"
                                        style="background-color: {{ $entry->category?->color ?? '#e2e8f0' }}"></span>
                                    <span class="font-medium text-gray-700 truncate">{{ $entry->name }}</span>
                                </div>
                                <span class="font-bold shrink-0 ml-2 text-rose-600">
                                    ${{ number_format($entry->spent, 2) }}
                                </span>
                            </div>
                            <div class="w-full bg-rose-50 rounded-full h-1.5 overflow-hidden">
                                <div class="h-1.5 bg-rose-400 rounded-full" style="width: 100%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @else
        <p class="text-gray-400 text-sm">No hay presupuesto activo.</p>
        <a href="{{ route('budgets.create', $group) }}" class="text-indigo-600 text-sm font-medium hover:underline">Crear
            presupuesto →</a>
    @endif
</div>