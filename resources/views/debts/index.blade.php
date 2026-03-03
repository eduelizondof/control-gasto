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

            {{-- Debt Capacity Analysis --}}
            @if($totalIncome > 0 && $debtPercent > 0)
                <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl p-6 text-white shadow-lg mb-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-10 h-10 bg-white/15 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold">Capacidad de Endeudamiento</h3>
                            <p class="text-slate-400 text-xs">Basado en ingreso de ${{ number_format($totalIncome, 2) }} y {{ $debtPercent }}% para deudas</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-5">
                        <div class="bg-white/10 rounded-xl p-3 border border-white/5">
                            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider mb-1">Capacidad Máxima</p>
                            <p class="text-xl font-black">${{ number_format($debtCapacity, 2) }}</p>
                            <p class="text-slate-500 text-[10px]">{{ $debtPercent }}% del ingreso</p>
                        </div>
                        <div class="bg-white/10 rounded-xl p-3 border border-white/5">
                            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider mb-1">Pagando Actualmente</p>
                            <p class="text-xl font-black text-amber-400">${{ number_format($totalMonthlyPayments, 2) }}</p>
                            <p class="text-slate-500 text-[10px]">mensual en deudas</p>
                        </div>
                        <div class="bg-white/10 rounded-xl p-3 border border-white/5">
                            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider mb-1">Capacidad Disponible</p>
                            <p class="text-xl font-black {{ $availableCapacity >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                                ${{ number_format(abs($availableCapacity), 2) }}
                            </p>
                            <p class="text-slate-500 text-[10px]">{{ $availableCapacity >= 0 ? 'libre para nuevas deudas' : 'excedido' }}</p>
                        </div>
                        <div class="bg-white/10 rounded-xl p-3 border border-white/5">
                            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider mb-1">Uso de Capacidad</p>
                            <p class="text-xl font-black {{ $usedPercent > 90 ? 'text-rose-400' : ($usedPercent > 70 ? 'text-amber-400' : 'text-emerald-400') }}">
                                {{ $usedPercent }}%
                            </p>
                            <p class="text-slate-500 text-[10px]">de tu límite</p>
                        </div>
                    </div>

                    {{-- Progress Bar --}}
                    <div class="w-full bg-white/10 rounded-full h-3 overflow-hidden">
                        @php
                            $barColor = $usedPercent > 90 ? 'bg-rose-500' : ($usedPercent > 70 ? 'bg-amber-500' : 'bg-emerald-500');
                        @endphp
                        <div class="h-3 {{ $barColor }} rounded-full transition-all duration-500" style="width: {{ min($usedPercent, 100) }}%"></div>
                    </div>
                    <div class="flex justify-between text-[10px] text-slate-500 mt-1">
                        <span>0%</span>
                        <span>{{ $usedPercent }}% utilizado</span>
                        <span>100%</span>
                    </div>
                </div>

                {{-- Capacity Timeline --}}
                @if($capacityTimeline->isNotEmpty())
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 mb-6">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800">Liberación de Capacidad</h3>
                                <p class="text-xs text-gray-400">Fechas en las que se libera capacidad al terminar pagos</p>
                            </div>
                        </div>

                        {{-- Current capacity --}}
                        <div class="flex items-start gap-4 mb-4">
                            <div class="flex flex-col items-center">
                                <div class="w-3 h-3 rounded-full {{ $availableCapacity >= 0 ? 'bg-emerald-500' : 'bg-rose-500' }} ring-4 {{ $availableCapacity >= 0 ? 'ring-emerald-100' : 'ring-rose-100' }}"></div>
                                <div class="w-0.5 h-full bg-gray-200 mt-1"></div>
                            </div>
                            <div class="pb-4">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Hoy</p>
                                <p class="text-sm font-bold text-gray-800">
                                    Capacidad disponible: 
                                    <span class="{{ $availableCapacity >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                        ${{ number_format(abs($availableCapacity), 2) }}{{ $availableCapacity < 0 ? ' (excedido)' : '' }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        {{-- Future releases --}}
                        @foreach($capacityTimeline as $item)
                            <div class="flex items-start gap-4 {{ !$loop->last ? 'mb-4' : '' }}">
                                <div class="flex flex-col items-center">
                                    <div class="w-3 h-3 rounded-full bg-indigo-500 ring-4 ring-indigo-100"></div>
                                    @if(!$loop->last)
                                        <div class="w-0.5 h-full bg-gray-200 mt-1"></div>
                                    @endif
                                </div>
                                <div class="{{ !$loop->last ? 'pb-4 border-b border-gray-50' : '' }} flex-1">
                                    <p class="text-xs font-bold text-indigo-600 uppercase tracking-wider">
                                        {{ $item->date->translatedFormat('d \d\e F Y') }}
                                        @if($item->remaining_payments)
                                            <span class="text-gray-400 normal-case font-normal">({{ $item->remaining_payments }} pagos restantes)</span>
                                        @endif
                                    </p>
                                    <p class="text-sm text-gray-800 mt-0.5">
                                        Se libera <span class="font-bold">{{ $item->name }}</span>
                                        → <span class="font-bold text-emerald-600">+${{ number_format($item->freed_amount, 2) }}/mes</span>
                                    </p>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        Capacidad disponible será: <span class="font-bold text-gray-700">${{ number_format($item->available_after, 2) }}/mes</span>
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @else
                <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-2xl p-6 border border-amber-100 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-amber-800">Configura tus ingresos</h3>
                            <p class="text-sm text-amber-700 mt-1">Para ver tu capacidad de endeudamiento, primero configura tus ingresos mensuales en la sección de presupuesto.</p>
                            <a href="{{ route('budget-configurations.index', $group) }}" class="inline-block mt-2 text-sm font-bold text-indigo-600 hover:underline">Ir a Configuración →</a>
                        </div>
                    </div>
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