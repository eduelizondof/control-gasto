<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-indigo-900 rounded-2xl p-6 text-white shadow-lg overflow-hidden relative">
        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold">Distribución de Pagos</h3>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white/10 rounded-xl p-3 border border-white/10">
                    <p class="text-indigo-100 text-[10px] font-bold uppercase mb-2">1a Quincena (1-15)</p>
                    <div class="flex justify-between items-end">
                        <div>
                            <p class="text-2xl font-black">${{ number_format($q1Load, 0) }}</p>
                            <p class="text-[10px] text-indigo-300">Total en pagos</p>
                        </div>
                        @if($q1Pending > 0)
                            <span
                                class="text-[10px] bg-amber-500/20 text-amber-300 px-1.5 py-0.5 rounded font-bold">${{ number_format($q1Pending, 0) }}
                                pend.</span>
                        @else
                            <span
                                class="text-[10px] bg-emerald-500/20 text-emerald-300 px-1.5 py-0.5 rounded font-bold">Cubierto</span>
                        @endif
                    </div>
                </div>
                <div class="bg-white/10 rounded-xl p-3 border border-white/10">
                    <p class="text-indigo-100 text-[10px] font-bold uppercase mb-2">2a Quincena (16-31)</p>
                    <div class="flex justify-between items-end">
                        <div>
                            <p class="text-2xl font-black">${{ number_format($q2Load, 0) }}</p>
                            <p class="text-[10px] text-indigo-300">Total en pagos</p>
                        </div>
                        @if($q2Pending > 0)
                            <span
                                class="text-[10px] bg-amber-500/20 text-amber-300 px-1.5 py-0.5 rounded font-bold">${{ number_format($q2Pending, 0) }}
                                pend.</span>
                        @else
                            <span
                                class="text-[10px] bg-emerald-500/20 text-emerald-300 px-1.5 py-0.5 rounded font-bold">Cubierto</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/5 rounded-full"></div>
    </div>

    <div
        class="bg-white rounded-2xl p-6 border {{ $q1Load > $q2Load ? 'border-amber-200 bg-amber-50/30' : 'border-indigo-100' }} flex items-center gap-4">
        <div
            class="w-12 h-12 {{ $q1Load > $q2Load ? 'bg-amber-100 text-amber-600' : 'bg-indigo-100 text-indigo-600' }} rounded-2xl flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                </path>
            </svg>
        </div>
        <div>
            <h4 class="font-bold text-gray-800">Consejo Quincenal</h4>
            <p class="text-sm text-gray-600 mt-1">
                @php
                    $isFirstQuincena = now()->day <= 15;
                @endphp
                @if($isFirstQuincena)
                    @if($q1Pending > 0)
                        Te faltan <span class="font-bold text-amber-600">${{ number_format($q1Pending, 0) }}</span> para cubrir
                        esta quincena.
                    @else
                        ¡Genial! Ya cubriste los pagos de esta quincena. Empieza a guardar para la siguiente (<span
                            class="font-bold text-indigo-600">${{ number_format($q2Pending, 0) }}</span> pendientes).
                    @endif
                @else
                    @if($q2Pending > 0)
                        Necesitas <span class="font-bold text-amber-600">${{ number_format($q2Pending, 0) }}</span> para los
                        pagos restantes del mes.
                    @else
                        Todos los pagos registrados para este mes han sido cubiertos.
                    @endif
                @endif
            </p>
        </div>
    </div>
</div>