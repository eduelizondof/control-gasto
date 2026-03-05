<div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col h-full">
    <div class="flex items-center justify-between mb-4 shrink-0">
        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Quincenas Próximas
        </h3>
    </div>

    <div class="space-y-3 flex-1 overflow-y-auto pr-1">
        @forelse($this->upcomingQuincenas as $quincena)
                <a href="{{ route('transactions.create', [
                'group' => $group,
                'payment_calendar_id' => $quincena->id
            ]) }}"
                    class="flex items-center justify-between p-3 rounded-xl bg-emerald-50 border border-emerald-100 hover:bg-emerald-100 transition-colors cursor-pointer group">
                    <div class="min-w-0">
                        <div class="font-bold text-emerald-800 text-sm truncate">{{ $quincena->concept }}</div>
                        <div class="text-emerald-700/80 text-xs font-medium mt-0.5 flex items-center gap-2">
                            <span class="flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                {{ \Carbon\Carbon::parse($quincena->payment_date)->translatedFormat('d M Y') }}
                            </span>
                            <span class="opacity-50">•</span>
                            <span>{{ $quincena->person_name }}</span>
                        </div>
                    </div>
                    <span class="font-bold text-emerald-600 text-md shrink-0 ml-2">
                        +${{ number_format($quincena->amount, 2) }}
                    </span>
                </a>
        @empty
            <div class="text-center py-6">
                <div class="bg-gray-50 rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                </div>
                <p class="text-gray-500 text-sm">No hay quincenas pendientes próximas.</p>
            </div>
        @endforelse
    </div>
</div>