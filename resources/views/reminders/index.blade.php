<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800">Recordatorios</h2>
            <a href="{{ route('reminders.create', $group) }}"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition shadow-sm">+
                Nuevo Recordatorio</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @php
                $typeLabels = ['fixed_payment' => 'Pago Fijo', 'card_cutoff' => 'Corte de Tarjeta', 'annuity' => 'Anualidad', 'expiration' => 'Vencimiento', 'debt' => 'Deuda', 'custom' => 'Personalizado'];
                $freqLabels = ['one_time' => 'Único', 'weekly' => 'Semanal', 'biweekly' => 'Quincenal', 'monthly' => 'Mensual', 'bimonthly' => 'Bimestral', 'quarterly' => 'Trimestral', 'semiannual' => 'Semestral', 'annual' => 'Anual'];
            @endphp

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($reminders as $reminder)
                    @php $isUpcoming = $reminder->next_date && $reminder->next_date->diffInDays(now()) <= ($reminder->advance_days ?? 3); @endphp
                    <div
                        class="bg-white rounded-2xl p-6 shadow-sm border {{ $isUpcoming ? 'border-amber-200 bg-amber-50/30' : 'border-gray-100' }} {{ !$reminder->is_active ? 'opacity-60' : '' }}">
                        <div class="flex items-center justify-between mb-3">
                            <span
                                class="px-2.5 py-1 rounded-lg text-xs font-medium bg-indigo-100 text-indigo-700">{{ $typeLabels[$reminder->type] ?? $reminder->type }}</span>
                            @if(!$reminder->is_active)
                                <span class="text-xs text-gray-400">Inactivo</span>
                            @elseif($isUpcoming)
                                <span class="text-xs text-amber-600 font-semibold animate-pulse">⚡ Próximo</span>
                            @endif
                        </div>

                        <h3 class="font-bold text-gray-800 text-lg mb-1">{{ $reminder->name }}</h3>

                        <div class="space-y-2 text-sm mb-4">
                            @if($reminder->estimated_amount)
                                <div class="text-2xl font-black text-gray-900">
                                    ${{ number_format($reminder->estimated_amount, 2) }}</div>
                            @endif
                            <div class="flex items-center gap-2 text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                <span>{{ $freqLabels[$reminder->frequency] ?? $reminder->frequency }}</span>
                                @if($reminder->day_of_month)
                                    <span>· Día {{ $reminder->day_of_month }}</span>
                                @endif
                            </div>
                            @if($reminder->next_date)
                                <div
                                    class="flex items-center gap-2 {{ $isUpcoming ? 'text-amber-600 font-semibold' : 'text-gray-500' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Próximo: {{ $reminder->next_date->translatedFormat('d M Y') }}
                                </div>
                            @endif
                            @if($reminder->account)
                                <div class="flex items-center gap-2 text-gray-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                        </path>
                                    </svg>
                                    {{ $reminder->account->name }}
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center gap-2 pt-3 border-t border-gray-100">
                            <a href="{{ route('reminders.edit', [$group, $reminder]) }}"
                                class="text-indigo-600 text-sm font-medium hover:underline">Editar</a>
                            <span class="text-gray-300">·</span>
                            <form method="POST" action="{{ route('reminders.destroy', [$group, $reminder]) }}"
                                onsubmit="return confirm('¿Eliminar este recordatorio?')">
                                @csrf @method('DELETE')
                                <button class="text-rose-500 text-sm font-medium hover:underline">Eliminar</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <p class="text-gray-500 mb-2">No hay recordatorios</p>
                        <a href="{{ route('reminders.create', $group) }}"
                            class="text-indigo-600 font-medium hover:underline">Crear recordatorio →</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>