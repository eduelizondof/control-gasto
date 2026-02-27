<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800">
            {{ isset($transaction) ? 'Editar Movimiento' : 'Nuevo Movimiento' }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                <form method="POST"
                    action="{{ isset($transaction) ? route('transactions.update', [$group, $transaction]) : route('transactions.store', $group) }}">
                    @csrf
                    @if(isset($transaction)) @method('PUT') @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="type" value="Tipo" />
                            <select id="type" name="type"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required>
                                @foreach(['income' => 'Ingreso', 'expense' => 'Gasto', 'transfer' => 'Transferencia', 'savings' => 'Ahorro', 'adjustment' => 'Ajuste'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('type', $transaction->type ?? 'expense') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label for="amount" value="Monto" />
                            <x-text-input id="amount" name="amount" type="number" step="0.01" min="0.01"
                                class="mt-1 block w-full" :value="old('amount', $transaction->amount ?? '')" required
                                placeholder="0.00" />
                            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="date" value="Fecha" />
                            <x-text-input id="date" name="date" type="date" class="mt-1 block w-full"
                                :value="old('date', isset($transaction) ? $transaction->date->format('Y-m-d') : now()->format('Y-m-d'))" required />
                        </div>

                        <div>
                            <x-input-label for="time" value="Hora (opcional)" />
                            <x-text-input id="time" name="time" type="time" class="mt-1 block w-full"
                                :value="old('time', $transaction->time ?? '')" />
                        </div>

                        <div>
                            <x-input-label for="category_id" value="Categoría" />
                            <select id="category_id" name="category_id"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required>
                                <option value="">Seleccionar...</option>
                                @php $grouped = $categories->groupBy('type'); @endphp
                                @foreach(['income' => 'Ingresos', 'expense' => 'Gastos', 'savings' => 'Ahorro', 'transfer' => 'Transferencia'] as $type => $label)
                                    @if(isset($grouped[$type]))
                                        <optgroup label="{{ $label }}">
                                            @foreach($grouped[$type] as $cat)
                                                <option value="{{ $cat->id }}" {{ old('category_id', $transaction->category_id ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label for="concept_id" value="Concepto (opcional)" />
                            <select id="concept_id" name="concept_id"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Sin concepto</option>
                                @foreach($concepts as $concept)
                                    <option value="{{ $concept->id }}" {{ old('concept_id', $transaction->concept_id ?? '') == $concept->id ? 'selected' : '' }}>{{ $concept->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label for="source_account_id" value="Cuenta origen" />
                            <select id="source_account_id" name="source_account_id"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}" {{ old('source_account_id', $transaction->source_account_id ?? '') == $acc->id ? 'selected' : '' }}>
                                        {{ $acc->name }} (${{ number_format($acc->current_balance, 2) }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="dest-account-wrap">
                            <x-input-label for="destination_account_id" value="Cuenta destino" />
                            <select id="destination_account_id" name="destination_account_id"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">No aplica</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}" {{ old('destination_account_id', $transaction->destination_account_id ?? '') == $acc->id ? 'selected' : '' }}>
                                        {{ $acc->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="description" value="Descripción" />
                            <x-text-input id="description" name="description" type="text" class="mt-1 block w-full"
                                :value="old('description', $transaction->description ?? '')"
                                placeholder="Ej: Compra en Walmart" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="notes" value="Notas (opcional)" />
                            <textarea id="notes" name="notes" rows="2"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Notas adicionales...">{{ old('notes', $transaction->notes ?? '') }}</textarea>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-8">
                        <a href="{{ route('transactions.index', $group) }}"
                            class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium text-sm">Cancelar</a>
                        <x-primary-button>{{ isset($transaction) ? 'Actualizar' : 'Registrar' }}
                            Movimiento</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>