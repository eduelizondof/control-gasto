<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800">
            {{ isset($reminder) ? 'Editar Recordatorio' : 'Nuevo Recordatorio' }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                <form method="POST"
                    action="{{ isset($reminder) ? route('reminders.update', [$group, $reminder]) : route('reminders.store', $group) }}">
                    @csrf
                    @if(isset($reminder)) @method('PUT') @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <x-input-label for="name" value="Nombre" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                :value="old('name', $reminder->name ?? '')" required placeholder="Ej: Pago de renta" />
                        </div>

                        <div>
                            <x-input-label for="type" value="Tipo" />
                            <select id="type" name="type"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required>
                                @foreach(['fixed_payment' => 'Pago Fijo', 'card_cutoff' => 'Corte de Tarjeta', 'annuity' => 'Anualidad', 'expiration' => 'Vencimiento', 'debt' => 'Deuda', 'custom' => 'Personalizado'] as $v => $l)
                                    <option value="{{$v}}" {{old('type', $reminder->type ?? '') === $v ? 'selected' : ''}}>{{$l}}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label for="frequency" value="Frecuencia" />
                            <select id="frequency" name="frequency"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required>
                                @foreach(['one_time' => 'Único', 'weekly' => 'Semanal', 'biweekly' => 'Quincenal', 'monthly' => 'Mensual', 'bimonthly' => 'Bimestral', 'quarterly' => 'Trimestral', 'semiannual' => 'Semestral', 'annual' => 'Anual'] as $v => $l)
                                    <option value="{{$v}}"
                                        {{old('frequency', $reminder->frequency ?? 'monthly') === $v ? 'selected' : ''}}>{{$l}}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label for="estimated_amount" value="Monto estimado" />
                            <x-text-input id="estimated_amount" name="estimated_amount" type="number" step="0.01"
                                class="mt-1 block w-full" :value="old('estimated_amount', $reminder->estimated_amount ?? '')" />
                        </div>

                        <div>
                            <x-input-label for="account_id" value="Cuenta" />
                            <select id="account_id" name="account_id"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Sin cuenta</option>
                                @foreach($accounts as $acc)
                                    <option value="{{$acc->id}}"
                                        {{old('account_id', $reminder->account_id ?? '') == $acc->id ? 'selected' : ''}}>
                                        {{$acc->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label for="day_of_month" value="Día del mes" />
                            <x-text-input id="day_of_month" name="day_of_month" type="number" min="1" max="31"
                                class="mt-1 block w-full" :value="old('day_of_month', $reminder->day_of_month ?? '')" />
                        </div>

                        <div>
                            <x-input-label for="advance_days" value="Avisar X días antes" />
                            <x-text-input id="advance_days" name="advance_days" type="number" min="0" max="30"
                                class="mt-1 block w-full" :value="old('advance_days', $reminder->advance_days ?? '3')" />
                        </div>

                        <div>
                            <x-input-label for="specific_date" value="Fecha específica" />
                            <x-text-input id="specific_date" name="specific_date" type="date" class="mt-1 block w-full"
                                :value="old('specific_date', isset($reminder) && $reminder->specific_date ? $reminder->specific_date->format('Y-m-d') : '')" />
                        </div>

                        <div class="flex items-end">
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="auto_create_transaction" value="1"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                    {{old('auto_create_transaction', $reminder->auto_create_transaction ?? false) ? 'checked' : ''}}>
                                <span class="text-sm text-gray-600">Crear movimiento automáticamente</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-8">
                        <a href="{{ route('reminders.index', $group) }}"
                            class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium text-sm">Cancelar</a>
                        <x-primary-button>{{ isset($reminder) ? 'Actualizar' : 'Crear' }}
                            Recordatorio</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>