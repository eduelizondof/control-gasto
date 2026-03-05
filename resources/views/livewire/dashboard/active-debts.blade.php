<div>
    @if($activeDebts->isNotEmpty())
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800">Deudas Activas</h3>
                <a href="{{ route('debts.index', $group) }}" class="text-indigo-600 text-sm font-medium hover:underline">Ver
                    todas →</a>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($activeDebts as $debt)
                    <div class="rounded-xl p-4 border border-gray-100">
                        <div class="flex justify-between items-start mb-2">
                            <div class="font-semibold text-gray-800 text-sm">{{ $debt->name }}</div>
                            @if($debt->next_payment_date)
                                <div class="text-right">
                                    <div class="text-xs font-bold text-indigo-600">${{ number_format($debt->payment_amount, 2) }}</div>
                                    <div class="text-[10px] text-gray-500">Próx:
                                        {{ $debt->next_payment_date->translatedFormat('d M Y') }}</div>
                                </div>
                            @endif
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 mb-2">
                            <span>Pagado: ${{ number_format($debt->paid_amount, 2) }}</span>
                            <span>Total: ${{ number_format($debt->total_amount, 2) }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                            <div class="h-2.5 bg-indigo-500 rounded-full" style="width: {{ $debt->progress_percent }}%"></div>
                        </div>
                        <div class="text-xs text-gray-400 mt-2">Pendiente: ${{ number_format($debt->outstanding_balance, 2) }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>