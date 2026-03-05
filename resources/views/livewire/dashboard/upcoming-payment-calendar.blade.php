<div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Quincenas Próximas
        </h3>
    </div>

    @if($this->upcomingQuincenas->isEmpty())
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
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($this->upcomingQuincenas as $quincena)
                <a href="{{ route('transactions.create', [
                    'group' => $group,
                    'payment_calendar_id' => $quincena->id
                ]) }}"
                    class="flex flex-col p-4 rounded-xl bg-emerald-50 border border-emerald-100 hover:bg-emerald-100 transition-colors cursor-pointer group shadow-sm">
                    <div class="flex items-start justify-between mb-2">
                        <div class="font-bold text-emerald-800 text-base truncate pr-2">{{ $quincena->concept }}</div>
                        <span class="font-bold text-emerald-600 text-lg shrink-0">
                            +${{ number_format($quincena->amount, 2) }}
                        </span>
                    </div>
                    <div class="text-emerald-700/80 text-sm font-medium mt-auto flex items-center gap-2">
                        <span class="flex items-center gap-1.5 bg-white/60 px-2 py-1 rounded-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            {{ \Carbon\Carbon::parse($quincena->payment_date)->translatedFormat('d M') }}
                        </span>
                        <span class="opacity-50">•</span>
                        <span class="truncate">{{ $quincena->person_name }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>