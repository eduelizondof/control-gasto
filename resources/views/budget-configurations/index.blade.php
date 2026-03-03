<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800">Configuración del Presupuesto</h2>
            <p class="text-sm text-gray-500">Grupo: {{ $group->name }}</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-3 gap-6" x-data="{
                fixedIncome: {{ old('fixed_monthly_income', $configuration->fixed_monthly_income) }},
                totalIncome: {{ old('total_monthly_income', $configuration->total_monthly_income) }},
                necessities: {{ old('necessities_percentage', $configuration->necessities_percentage) }},
                debts: {{ old('debts_percentage', $configuration->debts_percentage) }},
                future: {{ old('future_percentage', $configuration->future_percentage) }},
                desires: {{ old('desires_percentage', $configuration->desires_percentage) }},
                get total() {
                    return (parseFloat(this.necessities) || 0) + 
                           (parseFloat(this.debts) || 0) + 
                           (parseFloat(this.future) || 0) + 
                           (parseFloat(this.desires) || 0);
                },
                formatMoney(amount) {
                    return '$' + (parseFloat(amount) || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                },
                getCapacity(percentage) {
                    const pct = (parseFloat(percentage) || 0) / 100;
                    const fixed = (parseFloat(this.fixedIncome) || 0) * pct;
                    const total = (parseFloat(this.totalIncome) || 0) * pct;
                    
                    if (fixed === total) {
                         return this.formatMoney(fixed);
                    }
                    return this.formatMoney(fixed) + ' - ' + this.formatMoney(total);
                }
            }">
                <!-- Configuración de Porcentajes -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                            </svg>
                            Configuración del Presupuesto
                        </h3>

                        <form method="POST" action="{{ route('budget-configurations.update', $group) }}">
                            @csrf
                            @method('PATCH')

                            @if($errors->has('total'))
                                <div
                                    class="mb-4 p-3 bg-rose-50 border border-rose-100 text-rose-600 rounded-xl text-sm font-medium">
                                    {{ $errors->first('total') }}
                                </div>
                            @endif

                            {{-- Income Section --}}
                            <div
                                class="mb-6 p-4 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-xl border border-emerald-100">
                                <h4 class="font-bold text-emerald-800 mb-3 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                        </path>
                                    </svg>
                                    Ingresos Mensuales
                                </h4>
                                <div class="grid sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Ingreso Fijo
                                            Mensual</label>
                                        <input type="number" name="fixed_monthly_income" x-model="fixedIncome"
                                            step="0.01" min="0"
                                            class="w-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                                            placeholder="Ej: 30000 (quincenas × 2)">
                                        <p class="mt-1 text-xs text-gray-400">Suma de tus quincenas fijas</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Ingreso Total
                                            Mensual</label>
                                        <input type="number" name="total_monthly_income" x-model="totalIncome"
                                            step="0.01" min="0"
                                            class="w-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                                            placeholder="Ej: 35000 (con bonos, extras)">
                                        <p class="mt-1 text-xs text-gray-400">Incluye bonos, comisiones, extras</p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <!-- Necesidades -->
                                <div>
                                    <div class="flex justify-between items-center mb-1">
                                        <label class="block text-sm font-semibold text-gray-700">Necesidades
                                            (Operativo)</label>
                                        <span class="text-sm font-bold text-indigo-600"
                                            x-text="necessities + '%'"></span>
                                    </div>
                                    <input type="range" name="necessities_percentage" x-model="necessities" min="0"
                                        max="100" step="1"
                                        class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
                                    <p class="mt-1 text-xs text-gray-400">Súper, luz, agua, internet, gasolina, seguros,
                                        mantenimiento.</p>
                                </div>

                                <!-- Deudas -->
                                <div>
                                    <div class="flex justify-between items-center mb-1">
                                        <label class="block text-sm font-semibold text-gray-700">Deudas
                                            (Compromisos)</label>
                                        <span class="text-sm font-bold text-indigo-600" x-text="debts + '%'"></span>
                                    </div>
                                    <input type="range" name="debts_percentage" x-model="debts" min="0" max="100"
                                        step="1"
                                        class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
                                    <p class="mt-1 text-xs text-gray-400">Hipoteca, créditos personales, tarjetas de
                                        crédito (MSI).</p>
                                </div>

                                <!-- Futuro -->
                                <div>
                                    <div class="flex justify-between items-center mb-1">
                                        <label class="block text-sm font-semibold text-gray-700">Futuro
                                            (Ahorro/Inv.)</label>
                                        <span class="text-sm font-bold text-indigo-600" x-text="future + '%'"></span>
                                    </div>
                                    <input type="range" name="future_percentage" x-model="future" min="0" max="100"
                                        step="1"
                                        class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
                                    <p class="mt-1 text-xs text-gray-400">Fondo de emergencia, retiro, ahorro para
                                        enganches.</p>
                                </div>

                                <!-- Deseos -->
                                <div>
                                    <div class="flex justify-between items-center mb-1">
                                        <label class="block text-sm font-semibold text-gray-700">Deseos (Estilo de
                                            Vida)</label>
                                        <span class="text-sm font-bold text-indigo-600" x-text="desires + '%'"></span>
                                    </div>
                                    <input type="range" name="desires_percentage" x-model="desires" min="0" max="100"
                                        step="1"
                                        class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
                                    <p class="mt-1 text-xs text-gray-400">Salidas, streaming, hobbies, compras
                                        personales.</p>
                                </div>
                            </div>

                            <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-bold"
                                        :class="total === 100 ? 'text-emerald-600' : 'text-rose-500'">
                                        Total: <span x-text="total"></span>%
                                    </p>
                                    <p class="text-xs text-gray-400" x-show="total !== 100">La suma debe ser 100% para
                                        guardar.</p>
                                </div>
                                <button type="submit" :disabled="total !== 100"
                                    class="bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed text-white px-6 py-2.5 rounded-xl font-bold transition shadow-lg shadow-indigo-200">
                                    Guardar Configuración
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Guía Rápida -->
                <div class="space-y-6">
                    <div class="bg-indigo-600 rounded-2xl p-6 text-white shadow-lg shadow-indigo-200">
                        <h4 class="font-bold text-lg mb-2">Guía Financiera</h4>
                        <p class="text-indigo-100 text-sm leading-relaxed">
                            Para un manejo sano de las finanzas, sugerimos la regla 50/25/15/10.
                            Asegúrate de cubrir tus necesidades primero y destinar una parte al futuro.
                        </p>
                    </div>

                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <h4 class="font-bold text-gray-800 mb-4">Proyección de Presupuesto</h4>

                        <div class="space-y-3 mb-6">
                            <div class="flex flex-col text-sm border-b border-gray-50 pb-2">
                                <div class="flex justify-between w-full mb-1">
                                    <span class="text-gray-500 font-semibold">Necesidades</span>
                                    <span class="text-indigo-600 font-bold text-xs" x-text="necessities + '%'"></span>
                                </div>
                                <span class="font-bold text-gray-800" x-text="getCapacity(necessities)"></span>
                            </div>
                            <div class="flex flex-col text-sm border-b border-gray-50 pb-2">
                                <div class="flex justify-between w-full mb-1">
                                    <span class="text-gray-500 font-semibold">Deudas</span>
                                    <span class="text-indigo-600 font-bold text-xs" x-text="debts + '%'"></span>
                                </div>
                                <span class="font-bold text-gray-800" x-text="getCapacity(debts)"></span>
                            </div>
                            <div class="flex flex-col text-sm border-b border-gray-50 pb-2">
                                <div class="flex justify-between w-full mb-1">
                                    <span class="text-gray-500 font-semibold">Futuro</span>
                                    <span class="text-indigo-600 font-bold text-xs" x-text="future + '%'"></span>
                                </div>
                                <span class="font-bold text-gray-800" x-text="getCapacity(future)"></span>
                            </div>
                            <div class="flex flex-col text-sm">
                                <div class="flex justify-between w-full mb-1">
                                    <span class="text-gray-500 font-semibold">Deseos</span>
                                    <span class="text-indigo-600 font-bold text-xs" x-text="desires + '%'"></span>
                                </div>
                                <span class="font-bold text-gray-800" x-text="getCapacity(desires)"></span>
                            </div>
                        </div>

                        <div class="bg-indigo-50 rounded-xl p-4">
                            <h4 class="font-bold text-indigo-900 mb-2 text-sm flex items-center gap-2">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Resumen Sugerido
                            </h4>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-indigo-700">Necesidades</span>
                                    <span class="font-bold text-indigo-900">50%</span>
                                </div>
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-indigo-700">Deudas</span>
                                    <span class="font-bold text-indigo-900">20-30%</span>
                                </div>
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-indigo-700">Futuro</span>
                                    <span class="font-bold text-indigo-900">15-20%</span>
                                </div>
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-indigo-700">Deseos</span>
                                    <span class="font-bold text-indigo-900">10%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>