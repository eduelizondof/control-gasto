<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800">Configuración del Presupuesto</h2>
            <p class="text-sm text-gray-500">Grupo: {{ $group->name }}</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-3 gap-6">
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
                            Porcentajes de División
                        </h3>

                        <form method="POST" action="{{ route('budget-configurations.update', $group) }}" x-data="{
                            necessities: {{ old('necessities_percentage', $configuration->necessities_percentage) }},
                            debts: {{ old('debts_percentage', $configuration->debts_percentage) }},
                            future: {{ old('future_percentage', $configuration->future_percentage) }},
                            desires: {{ old('desires_percentage', $configuration->desires_percentage) }},
                            get total() {
                                return (parseFloat(this.necessities) || 0) + 
                                       (parseFloat(this.debts) || 0) + 
                                       (parseFloat(this.future) || 0) + 
                                       (parseFloat(this.desires) || 0);
                            }
                        }">
                            @csrf
                            @method('PATCH')

                            @if($errors->has('total'))
                                <div
                                    class="mb-4 p-3 bg-rose-50 border border-rose-100 text-rose-600 rounded-xl text-sm font-medium">
                                    {{ $errors->first('total') }}
                                </div>
                            @endif

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
                        <h4 class="font-bold text-gray-800 mb-4">Resumen Sugerido</h4>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">Necesidades</span>
                                <span class="font-bold text-gray-800">50%</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">Deudas</span>
                                <span class="font-bold text-gray-800">20-30%</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">Futuro</span>
                                <span class="font-bold text-gray-800">15-20%</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">Deseos</span>
                                <span class="font-bold text-gray-800">10%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>