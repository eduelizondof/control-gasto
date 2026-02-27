<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800">{{ isset($debt) ? 'Editar Deuda' : 'Nueva Deuda' }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                <form method="POST"
                    action="{{ isset($debt) ? route('debts.update', [$group, $debt]) : route('debts.store', $group) }}">
                    @csrf
                    @if(isset($debt)) @method('PUT') @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <x-input-label for="name" value="Nombre" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                :value="old('name', $debt->name ?? '')" required placeholder="Ej: Laptop 12 MSI" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="type" value="Tipo" />
                            <select id="type" name="type"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required>
                                @foreach(['revolving_credit' => 'Crédito Revolvente', 'no_interest_installments' => 'MSI', 'personal_loan' => 'Préstamo Personal', 'mortgage' => 'Hipoteca', 'auto_loan' => 'Automotriz', 'other' => 'Otro'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('type', $debt->type ?? '') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label for="account_id" value="Cuenta asociada" />
                            <select id="account_id" name="account_id"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Sin cuenta</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}" {{ old('account_id', $debt->account_id ?? '') == $acc->id ? 'selected' : '' }}>{{ $acc->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label for="total_amount" value="Monto total" />
                            <x-text-input id="total_amount" name="total_amount" type="number" step="0.01"
                                class="mt-1 block w-full" :value="old('total_amount', $debt->total_amount ?? '')"
                                required />
                        </div>

                        <div>
                            <x-input-label for="paid_amount" value="Monto pagado" />
                            <x-text-input id="paid_amount" name="paid_amount" type="number" step="0.01"
                                class="mt-1 block w-full" :value="old('paid_amount', $debt->paid_amount ?? '0')" />
                        </div>

                        <div>
                            <x-input-label for="interest_rate" value="Tasa de interés (%)" />
                            <x-text-input id="interest_rate" name="interest_rate" type="number" step="0.01"
                                class="mt-1 block w-full" :value="old('interest_rate', $debt->interest_rate ?? '0')" />
                        </div>

                        <div>
                            <x-input-label for="payment_amount" value="Mensualidad" />
                            <x-text-input id="payment_amount" name="payment_amount" type="number" step="0.01"
                                class="mt-1 block w-full" :value="old('payment_amount', $debt->payment_amount ?? '')" />
                        </div>

                        <div>
                            <x-input-label for="total_payments" value="Total de pagos" />
                            <x-text-input id="total_payments" name="total_payments" type="number"
                                class="mt-1 block w-full" :value="old('total_payments', $debt->total_payments ?? '')" />
                        </div>

                        <div>
                            <x-input-label for="start_date" value="Fecha de inicio" />
                            <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full"
                                :value="old('start_date', isset($debt) ? $debt->start_date->format('Y-m-d') : now()->format('Y-m-d'))" required />
                        </div>

                        <div>
                            <x-input-label for="end_date" value="Fecha de fin (opcional)" />
                            <x-text-input id="end_date" name="end_date" type="date" class="mt-1 block w-full"
                                :value="old('end_date', isset($debt) && $debt->end_date ? $debt->end_date->format('Y-m-d') : '')" />
                        </div>

                        <div>
                            <x-input-label for="next_payment_date" value="Próximo pago" />
                            <x-text-input id="next_payment_date" name="next_payment_date" type="date"
                                class="mt-1 block w-full" :value="old('next_payment_date', isset($debt) && $debt->next_payment_date ? $debt->next_payment_date->format('Y-m-d') : '')" />
                        </div>

                        @if(isset($debt))
                            <div>
                                <x-input-label for="status" value="Estatus" />
                                <select id="status" name="status"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @foreach(['active' => 'Activa', 'paid_off' => 'Liquidada', 'paused' => 'Pausada', 'overdue' => 'Vencida'] as $val => $label)
                                        <option value="{{ $val }}" {{ old('status', $debt->status) === $val ? 'selected' : '' }}>
                                            {{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="md:col-span-2">
                            <x-input-label for="notes" value="Notas (opcional)" />
                            <textarea id="notes" name="notes" rows="2"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes', $debt->notes ?? '') }}</textarea>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-8">
                        <a href="{{ route('debts.index', $group) }}"
                            class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium text-sm">Cancelar</a>
                        <x-primary-button>{{ isset($debt) ? 'Actualizar' : 'Registrar' }} Deuda</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>