<div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 mt-6 lg:col-span-3">
    @php
        $months = [1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'];
    @endphp

    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Bonos Esperados
        </h3>
        @if(!$isFormOpen)
            <button type="button" wire:click="openForm()"
                class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg text-sm font-semibold transition shadow-sm">
                + Nuevo Bono
            </button>
        @endif
    </div>

    @if (session()->has('success'))
        <div class="mb-4 bg-emerald-50 text-emerald-600 p-3 rounded-xl text-sm font-medium border border-emerald-100">
            {{ session('success') }}
        </div>
    @endif

    <!-- Inline Form -->
    @if($isFormOpen)
        <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200 mb-6">
            <div class="flex justify-between items-center mb-5">
                <h3 class="text-xl font-bold text-gray-800">{{ $bonusId ? 'Editar Bono' : 'Nuevo Bono' }}</h3>
                <button type="button" wire:click="closeForm" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <form wire:submit.prevent="save" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre del Bono</label>
                        <input type="text" wire:model="name"
                            class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                            placeholder="Ej: Aguinaldo">
                        @error('name') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Monto Estimado</label>
                        <input type="number" step="0.01" wire:model="amount"
                            class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                            placeholder="Ej: 30000">
                        @error('amount') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Mes</label>
                            <select wire:model="month"
                                class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                                <option value="">Selecciona...</option>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}">{{ $months[$i] }}</option>
                                @endfor
                            </select>
                            @error('month') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Día (Opcional)</label>
                            <input type="number" min="1" max="31" wire:model="day"
                                class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                                placeholder="Ej: 15">
                            @error('day') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex items-center gap-2 mt-6 md:mt-0">
                        <input type="checkbox" wire:model="is_active" id="is_active"
                            class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                        <label for="is_active" class="text-sm text-gray-700">Bono Activo</label>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-4 pt-4 border-t border-gray-200">
                    <button type="button" wire:click="closeForm"
                        class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium text-sm">Cancelar</button>
                    <button type="submit"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2 rounded-xl font-bold transition shadow-sm">
                        Guardar Bono
                    </button>
                </div>
            </form>
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-500 font-medium">
                <tr>
                    <th class="px-4 py-3 rounded-l-xl">Nombre</th>
                    <th class="px-4 py-3">Monto</th>
                    <th class="px-4 py-3">Mes/Día</th>
                    <th class="px-4 py-3 text-center">Estado</th>
                    <th class="px-4 py-3 text-right rounded-r-xl">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($this->bonuses as $bonus)
                    <tr class="hover:bg-gray-50/50 transition duration-150" wire:key="bonus-{{ $bonus->id }}">
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $bonus->name }}</td>
                        <td class="px-4 py-3 font-semibold text-emerald-600">${{ number_format($bonus->amount, 2) }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $months[$bonus->month] }}
                            {{ $bonus->day ? $bonus->day : '' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span
                                class="px-2 py-0.5 rounded text-xs {{ $bonus->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $bonus->is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button type="button" wire:click="openForm({{ $bonus->id }})"
                                class="text-indigo-600 hover:text-indigo-800 text-sm font-medium mr-3">Editar</button>
                            <button type="button" wire:click="delete({{ $bonus->id }})"
                                wire:confirm="¿Seguro que deseas eliminar el bono '{{ $bonus->name }}'?"
                                class="text-rose-500 hover:text-rose-700 text-sm font-medium">Eliminar</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-400">
                            No hay bonos registrados. Registra tu aguinaldo, fondo de ahorro o utilidades.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>