<div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
    <h3 class="text-lg font-bold text-gray-800 mb-4">Presupuesto vs Gasto Real</h3>
    @if($activeBudget)
        @php
            $budgetDiff = $budgetTotal - $budgetSpent;
            $budgetPercent = $budgetTotal > 0 ? min(100, round(($budgetSpent / $budgetTotal) * 100)) : 0;
        @endphp
        {{-- Global bar --}}
        @php
            $isOverBudget = $budgetSpent > $budgetTotal;
            $isExactBudget = $budgetSpent == $budgetTotal && $budgetTotal > 0;
            $isUnderBudget = $budgetSpent < $budgetTotal;

            $globalBgClass = $isOverBudget ? 'bg-rose-500' : ($isExactBudget ? 'bg-emerald-500' : ($isUnderBudget ? 'bg-blue-500' : 'bg-gray-400'));
            $globalTextClass = $isOverBudget ? 'text-rose-600' : ($isExactBudget ? 'text-emerald-600' : ($isUnderBudget ? 'text-blue-600' : 'text-gray-500'));
        @endphp
        <div class="mb-5">
            <div class="flex justify-between text-sm mb-2">
                <span class="text-gray-600">Gastado: ${{ number_format($budgetSpent, 2) }}</span>
                <span class="text-gray-600">de ${{ number_format($budgetTotal, 2) }}</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-4 overflow-hidden relative">
                <div class="h-4 rounded-full transition-all duration-500 {{ $globalBgClass }}"
                    style="width: {{ $budgetPercent }}%"></div>
            </div>

            <div class="mt-3 flex items-center justify-between">
                <p class="text-sm font-semibold {{ $globalTextClass }}">
                    @if($isOverBudget)
                        Excedido: ${{ number_format(abs($budgetDiff), 2) }}
                    @elseif($isExactBudget)
                        Presupuesto exacto
                    @elseif($isUnderBudget)
                        Disponible: ${{ number_format($budgetDiff, 2) }}
                    @else
                        Sin presupuesto
                    @endif
                </p>

                @if($isOverBudget)
                    <span
                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-rose-100 text-rose-800 border border-rose-200">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                        +${{ number_format(abs($budgetDiff), 2) }} sobre límite
                    </span>
                @endif
            </div>
        </div>

        {{-- Per-concept breakdown --}}
        @if($budgetBreakdown->isNotEmpty())
            <div class="border-t border-gray-100 pt-4">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Desglose por concepto</p>
                <div class="space-y-3 max-h-64 overflow-y-auto pr-1">
                    @foreach($budgetBreakdown as $entry)
                        @php
                            $itemIsOver = $entry->spent > $entry->budgeted;
                            $itemIsExact = $entry->spent == $entry->budgeted && $entry->budgeted > 0;
                            $itemIsUnder = $entry->spent < $entry->budgeted;

                            $itemBgClass = $itemIsOver ? 'bg-rose-400' : ($itemIsExact ? 'bg-emerald-400' : ($itemIsUnder ? 'bg-blue-400' : 'bg-gray-400'));
                            $itemTextClass = $itemIsOver ? 'text-rose-600' : ($itemIsExact ? 'text-emerald-600' : ($itemIsUnder ? 'text-blue-600' : 'text-gray-500'));

                            $diffText = '';
                            if ($itemIsOver) {
                                $diffText = '+$' . number_format(abs($entry->diff), 2);
                            } elseif ($itemIsExact) {
                                $diffText = 'Exacto';
                            } else {
                                $diffText = '-$' . number_format($entry->diff, 2);
                            }
                        @endphp
                        <a href="{{ route('transactions.create', ['group' => $group, 'type' => 'expense', 'category_id' => $entry->category_id, 'concept_id' => $entry->concept_id]) }}"
                            class="block hover:bg-gray-50 p-1.5 -mx-1.5 rounded-lg transition-colors group">
                            <div class="flex items-center justify-between text-xs mb-1">
                                <div class="flex items-center gap-1.5 min-w-0">
                                    <span class="w-2 h-2 rounded-full shrink-0"
                                        style="background-color: {{ $entry->category->color }}"></span>
                                    <span class="font-medium text-gray-700 truncate">{{ $entry->name }}</span>
                                </div>
                                <div class="flex items-center gap-2 shrink-0 ml-2">
                                    <span class="font-bold {{ $itemTextClass }}">
                                        {{ $diffText }}
                                    </span>
                                </div>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                <div class="h-1.5 rounded-full transition-all duration-300 {{ $itemBgClass }}"
                                    style="width: {{ min(100, $entry->percent) }}%"></div>
                            </div>
                            <div
                                class="flex justify-between text-[10px] text-gray-400 mt-0.5 group-hover:text-gray-500 transition-colors">
                                <span>${{ number_format($entry->spent, 2) }} gastado</span>
                                <span>${{ number_format($entry->budgeted, 2) }} presup.</span>
                            </div>
                        </a>
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
                        <a href="{{ route('transactions.create', ['group' => $group, 'type' => 'expense', 'category_id' => $entry->category_id, 'concept_id' => $entry->concept_id]) }}"
                            class="block hover:bg-rose-50 p-1.5 -mx-1.5 rounded-lg transition-colors group">
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
                        </a>
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