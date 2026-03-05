<div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col h-full">
    <div class="flex items-center justify-between mb-4 shrink-0">
        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Próximos Pagos vs. Bonos
        </h3>
    </div>

    <div class="space-y-6 flex-1 overflow-y-auto pr-1">
        {{-- Bonos Esperados --}}
        <div>
            <h4 class="text-xs font-bold text-emerald-600 uppercase tracking-wider mb-2">Bonos Esperados</h4>
            <div class="space-y-2">
                @forelse($bonuses as $bonus)
                    <a href="{{ route('transactions.create', ['group' => $group, 'type' => 'income', 'amount' => $bonus->amount, 'description' => 'Bono ' . $bonus->name]) }}"
                        class="flex items-center justify-between p-2.5 rounded-xl bg-emerald-50 border border-emerald-100 hover:bg-emerald-100 transition-colors cursor-pointer group">
                        <div class="min-w-0">
                            <div class="font-bold text-emerald-800 text-sm truncate">{{ $bonus->name }}</div>
                            <div class="text-emerald-700/80 text-xs font-medium">
                                {{ \Carbon\Carbon::createFromDate(null, $bonus->month, null)->translatedFormat('F') }}
                                {{ $bonus->day ? $bonus->day : '' }}
                            </div>
                        </div>
                        <span class="font-bold text-emerald-600 text-md shrink-0 ml-2">
                            +${{ number_format($bonus->amount, 2) }}
                        </span>
                    </a>
                @empty
                    <p class="text-gray-400 text-xs py-2">No hay bonos registrados.</p>
                @endforelse
            </div>
        </div>

        {{-- Pagos --}}
        <div>
            <h4 class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-2">Próximos Pagos</h4>
            <div class="space-y-2">
                @forelse($payments as $payment)
                    <a href="{{ route('transactions.create', ['group' => $group, 'type' => 'expense', 'amount' => $payment->estimated_amount, 'category_id' => $payment->category_id, 'concept_id' => $payment->concept_id, 'description' => $payment->name]) }}"
                        class="flex items-center justify-between p-2.5 rounded-xl bg-amber-50 border border-amber-100 hover:bg-amber-100 transition-colors cursor-pointer group">
                        <div class="min-w-0">
                            <div class="font-bold text-amber-800 text-sm truncate">{{ $payment->name }}</div>
                            <div class="text-amber-700/80 text-xs font-medium">
                                {{ $payment->next_date ? $payment->next_date->translatedFormat('d M Y') : 'Sin fecha' }}
                            </div>
                        </div>
                        <span class="font-bold text-rose-600 text-md shrink-0 ml-2">
                            -${{ number_format($payment->estimated_amount, 2) }}
                        </span>
                    </a>
                @empty
                    <p class="text-gray-400 text-xs py-2">No hay pagos próximos.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>