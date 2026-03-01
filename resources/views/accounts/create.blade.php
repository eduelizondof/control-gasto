<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800">{{ isset($account) ? 'Editar Cuenta' : 'Nueva Cuenta' }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                <form method="POST"
                    action="{{ isset($account) ? route('accounts.update', [$group, $account]) : route('accounts.store', $group) }}">
                    @csrf
                    @if(isset($account)) @method('PUT') @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="name" value="Nombre" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                :value="old('name', $account->name ?? '')" required placeholder="Ej: BBVA Débito" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="type" value="Tipo de cuenta" />
                            <select id="type" name="type"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required>
                                <option value="">Seleccionar...</option>
                                @foreach(['cash' => 'Efectivo', 'debit' => 'Débito', 'credit' => 'Crédito', 'investment' => 'Inversión', 'savings' => 'Ahorro', 'emergency' => 'Emergencias', 'fund' => 'Fondo'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('type', $account->type ?? '') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="bank" value="Banco (opcional)" />
                            <x-text-input id="bank" name="bank" type="text" class="mt-1 block w-full"
                                :value="old('bank', $account->bank ?? '')" placeholder="Ej: BBVA" />
                        </div>

                        <div>
                            @if(isset($account))
                                {{-- En edición el saldo es calculado; solo lectura --}}
                                <x-input-label for="current_balance_display" value="Saldo actual" />
                                <div class="mt-1 flex items-center gap-2">
                                    <x-text-input id="current_balance_display" type="text"
                                        class="block w-full bg-gray-50 text-gray-500 cursor-not-allowed"
                                        value="{{ number_format($account->current_balance, 2) }}" readonly disabled />
                                </div>
                                <p class="mt-1.5 flex items-start gap-1.5 text-xs text-amber-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mt-0.5 flex-shrink-0"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    El saldo se actualiza automáticamente al registrar ingresos, gastos o transferencias. No
                                    es editable directamente.
                                </p>
                            @else
                                <x-input-label for="initial_balance" value="Saldo inicial" />
                                <x-text-input id="initial_balance" name="initial_balance" type="number" step="0.01"
                                    class="mt-1 block w-full" :value="old('initial_balance', '0')" required />
                                <p class="mt-1 text-xs text-gray-400">Este monto será el punto de partida de la cuenta.</p>
                            @endif
                        </div>

                        <div>
                            <x-input-label for="color" value="Color" />
                            <input id="color" name="color" type="color"
                                class="mt-1 block w-16 h-10 border-gray-300 rounded-md"
                                value="{{ old('color', $account->color ?? '#6366F1') }}">
                        </div>

                        <div>
                            <x-input-label for="currency" value="Moneda" />
                            <select id="currency" name="currency"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="MXN" {{ old('currency', $account->currency ?? 'MXN') === 'MXN' ? 'selected' : '' }}>MXN - Peso Mexicano</option>
                                <option value="USD" {{ old('currency', $account->currency ?? '') === 'USD' ? 'selected' : '' }}>USD - Dólar</option>
                            </select>
                        </div>
                    </div>

                    <!-- Credit card fields -->
                    <div id="credit-fields" class="mt-6 p-4 bg-purple-50 rounded-xl border border-purple-100 hidden">
                        <h4 class="font-semibold text-purple-800 mb-4">Configuración de Tarjeta de Crédito</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <x-input-label for="credit_limit" value="Límite de crédito" />
                                <x-text-input id="credit_limit" name="credit_limit" type="number" step="0.01"
                                    class="mt-1 block w-full" :value="old('credit_limit', $account->credit_limit ?? '')" />
                            </div>
                            <div>
                                <x-input-label for="cutoff_day" value="Día de corte" />
                                <x-text-input id="cutoff_day" name="cutoff_day" type="number" min="1" max="31"
                                    class="mt-1 block w-full" :value="old('cutoff_day', $account->cutoff_day ?? '')" />
                            </div>
                            <div>
                                <x-input-label for="payment_day" value="Día de pago" />
                                <x-text-input id="payment_day" name="payment_day" type="number" min="1" max="31"
                                    class="mt-1 block w-full" :value="old('payment_day', $account->payment_day ?? '')" />
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center gap-4">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="include_in_total" value="1"
                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" {{ old('include_in_total', $account->include_in_total ?? true) ? 'checked' : '' }}>
                            <span class="text-sm text-gray-600">Incluir en saldo total</span>
                        </label>
                    </div>

                    <div class="flex justify-end gap-3 mt-8">
                        <a href="{{ route('accounts.index', $group) }}"
                            class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium text-sm">Cancelar</a>
                        <x-primary-button>{{ isset($account) ? 'Actualizar' : 'Crear' }} Cuenta</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const typeSelect = document.getElementById('type');
            const creditFields = document.getElementById('credit-fields');

            function toggleCreditFields() {
                creditFields.classList.toggle('hidden', typeSelect.value !== 'credit');
            }

            typeSelect.addEventListener('change', toggleCreditFields);
            toggleCreditFields();
        });
    </script>
</x-app-layout>