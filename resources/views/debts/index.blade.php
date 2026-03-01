<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800">Deudas</h2>
            <a href="{{ route('debts.create', $group) }}"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition shadow-sm">+
                Nueva Deuda</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Debt Limits --}}
            @if($debtLimits->isNotEmpty())
                <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-2xl p-6 border border-amber-100 mb-6">
                    <h3 class="font-bold text-amber-800 mb-3">Capacidad de Endeudamiento</h3>
                    @foreach($debtLimits as $limit)
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm text-amber-700">{{ $limit->name }}</span>
                            <span class="font-bold text-amber-900">Disponible: ${{ number_format($limit->available_amount, 2) }}
                                / ${{ number_format($limit->max_amount, 2) }}</span>
                        </div>
                        <div class="w-full bg-amber-200/50 rounded-full h-3">
                            @php $usedPercent = $limit->max_amount > 0 ? min(100, round(($limit->committed_amount / $limit->max_amount) * 100)) : 0; @endphp
                            <div class="h-3 bg-amber-500 rounded-full" style="width: {{ $usedPercent }}%"></div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Debts List --}}
            <div class="grid md:grid-cols-2 gap-6">
                @forelse($debts as $debt)
                    @php
                        $statusColors = ['active' => 'bg-emerald-100 text-emerald-700', 'paid_off' => 'bg-blue-100 text-blue-700', 'paused' => 'bg-amber-100 text-amber-700', 'overdue' => 'bg-rose-100 text-rose-700'];
                        $statusLabels = ['active' => 'Activa', 'paid_off' => 'Liquidada', 'paused' => 'Pausada', 'overdue' => 'Vencida'];
                        $typeLabels = ['revolving_credit' => 'Crédito Revolvente', 'no_interest_installments' => 'MSI', 'personal_loan' => 'Préstamo Personal', 'mortgage' => 'Hipoteca', 'auto_loan' => 'Automotriz', 'other' => 'Otro'];
                    @endphp
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="font-bold text-gray-800">{{ $debt->name }}</h3>
                                <p class="text-gray-400 text-xs">{{ $typeLabels[$debt->type] ?? $debt->type }}
                                    {{ $debt->account ? '· ' . $debt->account->name : '' }}</p>
                            </div>
                            <span
                                class="px-2.5 py-1 rounded-lg text-xs font-medium {{ $statusColors[$debt->status] ?? '' }}">{{ $statusLabels[$debt->status] ?? $debt->status }}</span>
                        </div>

                        <div class="mb-3">
                            <div class="flex justify-between text-sm text-gray-500 mb-1">
                                <span>Pagado: ${{ number_format($debt->paid_amount, 2) }}</span>
                                <span>Total: ${{ number_format($debt->total_amount, 2) }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-3">
                                <div class="h-3 bg-indigo-500 rounded-full transition-all"
                                    style="width: {{ $debt->progress_percent }}%"></div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-sm mb-4">
                            <div>
                                <span class="text-gray-400 text-xs">Pendiente</span>
                                <p class="font-bold text-gray-800">${{ number_format($debt->outstanding_balance, 2) }}</p>
                            </div>
                            @if($debt->payment_amount)
                                <div>
                                    <span class="text-gray-400 text-xs">Mensualidad</span>
                                    <p class="font-bold text-gray-800">${{ number_format($debt->payment_amount, 2) }}</p>
                                </div>
                            @endif
                            @if($debt->next_payment_date)
                                <div>
                                    <span class="text-gray-400 text-xs">Próximo pago</span>
                                    <p class="font-medium text-gray-600">{{ $debt->next_payment_date->format('d/m/Y') }}</p>
                                </div>
                            @endif
                            @if($debt->total_payments)
                                <div>
                                    <span class="text-gray-400 text-xs">Pagos</span>
                                    <p class="font-medium text-gray-600">{{ $debt->payments_made }}/{{ $debt->total_payments }}
                                    </p>
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center gap-2 pt-3 border-t border-gray-50">
                            <a href="{{ route('debts.edit', [$group, $debt]) }}"
                                class="text-indigo-600 text-sm font-medium hover:underline">Editar</a>
                            <span class="text-gray-300">·</span>
                            <form method="POST" action="{{ route('debts.destroy', [$group, $debt]) }}"
                                data-confirm="Se eliminará la deuda '{{ $debt->name }}' (${{ number_format($debt->outstanding_balance, 2) }} pendiente)."
                                data-title="¿Eliminar deuda?"
                                data-btn-text="Sí, eliminar">
                                @csrf @method('DELETE')
                                <button class="text-rose-500 text-sm font-medium hover:underline">Eliminar</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <p class="text-gray-500 mb-2">No hay deudas registradas 🎉</p>
                        <a href="{{ route('debts.create', $group) }}"
                            class="text-indigo-600 font-medium hover:underline">Registrar deuda →</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>