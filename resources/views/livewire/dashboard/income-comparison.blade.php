<div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                </path>
            </svg>
            Ingreso: Base vs Real
        </div>
        <span class="text-[10px] font-bold text-gray-400 uppercase bg-gray-100 px-2 py-0.5 rounded-full">Base:
            Fijo</span>
    </h3>

    <div class="space-y-4">
        {{-- Main Indicator --}}
        <div
            class="p-4 bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl border border-emerald-100 relative overflow-hidden">
            <p class="text-xs font-bold text-emerald-600 uppercase tracking-wider mb-1 relative z-10">Promedio Real
                Mensual</p>
            <div class="flex items-baseline gap-2 relative z-10">
                <span class="text-3xl font-black text-emerald-700">${{ number_format($avgIncome12m, 0) }}</span>
                <span class="text-sm font-medium text-emerald-600/70">/ mes</span>
            </div>

            {{-- Visual Range Indicator --}}
            <div class="mt-4 relative pt-5">
                @php
                    $maxVal = max($avgIncome12m, $configuredIncome, $configuredFixedIncome * 1.2);
                    $fixedPos = $maxVal > 0 ? ($configuredFixedIncome / $maxVal) * 100 : 0;
                    $totalPos = $maxVal > 0 ? ($configuredIncome / $maxVal) * 100 : 0;
                    $avgPos = $maxVal > 0 ? ($avgIncome12m / $maxVal) * 100 : 0;
                @endphp

                {{-- Background Track --}}
                <div class="h-2 w-full bg-gray-200 rounded-full relative">
                    {{-- Sporadic Range (Fixed to Total) --}}
                    <div class="absolute h-2 bg-emerald-200 opacity-50 rounded-full"
                        style="left: {{ $fixedPos }}%; width: {{ max(0, $totalPos - $fixedPos) }}%"></div>

                    {{-- Markers --}}
                    <div class="absolute -top-5 text-[9px] font-bold text-gray-400"
                        style="left: {{ $fixedPos }}%; transform: translateX(-50%)">
                        FIJO
                    </div>
                    <div class="absolute h-4 w-0.5 bg-gray-400 top-0" style="left: {{ $fixedPos }}%"></div>

                    @if($configuredIncome > $configuredFixedIncome)
                        <div class="absolute -top-5 text-[9px] font-bold text-emerald-500"
                            style="left: {{ $totalPos }}%; transform: translateX(-50%)">
                            +SPORAD.
                        </div>
                        <div class="absolute h-4 w-0.5 bg-emerald-400 top-0" style="left: {{ $totalPos }}%"></div>
                    @endif

                    {{-- Actual Average Marker --}}
                    <div class="absolute -bottom-1 h-4 w-4 bg-emerald-600 rounded-full border-2 border-white shadow-sm"
                        style="left: {{ $avgPos }}%; transform: translate(-50%, -25%); z-index: 20;"></div>
                </div>
            </div>

            {{-- Decorative light --}}
            <div class="absolute -right-4 -bottom-4 w-16 h-16 bg-emerald-200/30 rounded-full blur-2xl"></div>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Presupuesto Base</p>
                <p class="text-sm font-black text-gray-700">${{ number_format($configuredFixedIncome, 0) }}</p>
            </div>
            <div class="p-3 bg-indigo-50 rounded-xl border border-indigo-100 text-right">
                <p class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider mb-1">Diferencia vs Fijo</p>
                <p class="text-sm font-black text-indigo-700">
                    {{ $incomeDiff >= 0 ? '+' : '-' }}${{ number_format(abs($incomeDiff), 0) }}
                </p>
            </div>
        </div>

        @if($incomeDiff != 0)
            <div
                class="flex items-center gap-2 p-2 px-3 rounded-lg {{ $incomeDiff >= 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }} text-[11px] font-semibold">
                <span>{{ $incomeDiff >= 0 ? '▲' : '▼' }} {{ abs($incomeDiffPercent) }}%</span>
                <span
                    class="opacity-70">{{ $incomeDiff >= 0 ? 'sobre tu ingreso fijo base.' : 'por debajo del ingreso fijo.' }}</span>
            </div>
        @endif

        <p class="text-[10px] text-gray-400 italic text-center px-4">
            Los ingresos esporádicos (bonos, aguinaldos) ayudan a cubrir gastos, pero tu base está en los
            ${{ number_format($configuredFixedIncome, 0) }}.
        </p>
    </div>
</div>