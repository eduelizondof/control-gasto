<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800">Editar Presupuesto: {{ $budget->name }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Budget Info --}}
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 mb-6">
                <form method="POST" action="{{ route('budgets.update', [$group, $budget]) }}">
                    @csrf @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="name" value="Nombre del presupuesto" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $budget->name)" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                        <div class="flex items-end gap-4">
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" {{ $budget->is_active ? 'checked' : '' }}>
                                <span class="text-sm text-gray-600">Presupuesto activo</span>
                            </label>
                            <x-primary-button>Guardar</x-primary-button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Existing Items --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-6">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800">Items del Presupuesto</h3>
                    <p class="text-sm text-gray-500 mt-1">Total mensual: <span class="font-bold text-gray-800">${{ number_format($budget->items->where('is_active', true)->sum('monthly_amount'), 2) }}</span></p>
                </div>

                @if($budget->items->isNotEmpty())
                    <div class="divide-y divide-gray-50">
                        @php
                            $freqLabels = ['monthly'=>'Mensual','bimonthly'=>'Bimestral','quarterly'=>'Trimestral','semiannual'=>'Semestral','annual'=>'Anual'];
                        @endphp
                        @foreach($budget->items as $item)
                            <div class="p-4 hover:bg-gray-50/50 flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-3 h-3 rounded-full shrink-0" style="background-color: {{ $item->category->color }}"></div>
                                    <div class="min-w-0">
                                        <div class="font-medium text-gray-800 text-sm truncate">{{ $item->concept?->name ?? $item->custom_name ?? $item->category->name }}</div>
                                        <div class="text-xs text-gray-400">{{ $item->category->name }} · {{ $freqLabels[$item->frequency] ?? $item->frequency }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4 shrink-0">
                                    <div class="text-right">
                                        <div class="font-bold text-gray-800 text-sm">${{ number_format($item->monthly_amount, 2) }}/mes</div>
                                        <div class="text-xs text-gray-400">${{ number_format($item->estimated_amount, 2) }} {{ $item->frequency !== 'monthly' ? '(' . ($freqLabels[$item->frequency] ?? '') . ')' : '' }}</div>
                                    </div>
                                    <span class="px-2 py-0.5 rounded text-xs {{ $item->is_fixed ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ $item->is_fixed ? 'Fijo' : 'Variable' }}
                                    </span>
                                    {{-- Inline edit form --}}
                                    <form method="POST" action="{{ route('budgets.update-item', [$group, $budget, $item]) }}" class="flex items-center gap-2">
                                        @csrf @method('PATCH')
                                        <input type="number" step="0.01" name="estimated_amount" value="{{ $item->estimated_amount }}" class="w-24 border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500 py-1">
                                        <select name="frequency" class="border-gray-300 rounded-md shadow-sm text-xs focus:border-indigo-500 focus:ring-indigo-500 py-1">
                                            @foreach($freqLabels as $v => $l)
                                                <option value="{{ $v }}" {{ $item->frequency === $v ? 'selected' : '' }}>{{ $l }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">Actualizar</button>
                                    </form>
                                    <form method="POST" action="{{ route('budgets.delete-item', [$group, $budget, $item]) }}" onsubmit="return confirm('¿Eliminar este item?')">
                                        @csrf @method('DELETE')
                                        <button class="text-rose-500 hover:text-rose-700 text-xs font-medium">×</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-6 text-center text-gray-400 text-sm">No hay items en este presupuesto.</div>
                @endif
            </div>

            {{-- Add New Item --}}
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Agregar Nuevo Item</h3>
                <form method="POST" action="{{ route('budgets.add-item', [$group, $budget]) }}">
                    @csrf
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                        <div>
                            <label class="text-xs text-gray-500 font-medium">Categoría</label>
                            <select name="category_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Seleccionar...</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 font-medium">Nombre / Concepto</label>
                            <input type="text" name="custom_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Ej: Renta" required>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 font-medium">Monto estimado</label>
                            <input type="number" step="0.01" name="estimated_amount" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 font-medium">Frecuencia</label>
                            <select name="frequency" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="monthly">Mensual</option>
                                <option value="bimonthly">Bimestral</option>
                                <option value="quarterly">Trimestral</option>
                                <option value="semiannual">Semestral</option>
                                <option value="annual">Anual</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                                + Agregar
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="mt-4">
                <a href="{{ route('budgets.index', $group) }}" class="text-gray-500 hover:text-gray-700 text-sm">← Volver a presupuestos</a>
            </div>
        </div>
    </div>
</x-app-layout>