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
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <x-input-label for="name" value="Nombre del presupuesto" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                :value="old('name', $budget->name)" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="year" value="Año" />
                            <select id="year" name="year"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                required>
                                @for($i = date('Y') - 1; $i <= date('Y') + 5; $i++)
                                    <option value="{{ $i }}" {{ old('year', $budget->year ?? date('Y')) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                            <x-input-error :messages="$errors->get('year')" class="mt-2" />
                        </div>
                        <div class="flex items-end gap-4">
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="is_active" value="1"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" {{ $budget->is_active ? 'checked' : '' }}>
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
                    <p class="text-sm text-gray-500 mt-1">Total mensual: <span
                            class="font-bold text-gray-800">${{ number_format($budget->items->where('is_active', true)->sum('monthly_amount'), 2) }}</span>
                    </p>
                </div>

                @if($budget->items->isNotEmpty())
                    <div class="divide-y divide-gray-50">
                        @php
                            $freqLabels = ['monthly' => 'Mensual', 'bimonthly' => 'Bimestral', 'quarterly' => 'Trimestral', 'semiannual' => 'Semestral', 'annual' => 'Anual'];
                        @endphp
                        @foreach($budget->items as $item)
                            <div class="p-4 hover:bg-gray-50/50 flex flex-col gap-3 group">
                                {{-- Header Row: Info --}}
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-3 h-3 rounded-full shrink-0" style="background-color: {{ $item->category->color }}"></div>
                                        <div class="min-w-0">
                                            <div class="font-medium text-gray-800 text-sm truncate">
                                                {{ $item->concept?->name ?? $item->custom_name ?? $item->category->name }}
                                            </div>
                                            <div class="text-xs text-gray-400">
                                                {{ $item->category->name }} · {{ $freqLabels[$item->frequency] ?? $item->frequency }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 shrink-0">
                                        <div class="text-right">
                                            <div class="font-bold text-gray-800 text-sm">
                                                ${{ number_format($item->monthly_amount, 2) }}<span class="text-gray-400 font-normal">/mes</span>
                                            </div>
                                            <div class="text-xs text-gray-400 xl:hidden">
                                                ${{ number_format($item->estimated_amount, 2) }}
                                            </div>
                                        </div>
                                        <span class="hidden sm:inline-block px-2 py-0.5 rounded text-xs {{ $item->is_fixed ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700' }}">
                                            {{ $item->is_fixed ? 'Fijo' : 'Variable' }}
                                        </span>
                                    </div>
                                </div>
                            
                                {{-- Form Row (Inputs & Actions) --}}
                                <div class="flex flex-col xl:flex-row xl:items-center gap-3 mt-1">
                                    <form method="POST" action="{{ route('budgets.update-item', [$group, $budget, $item]) }}" class="flex-1 w-full" id="form-edit-{{ $item->id }}">
                                        @csrf @method('PATCH')
                                        <div class="flex flex-wrap items-center gap-2">
                                            <input type="number" step="0.01" name="estimated_amount"
                                                value="{{ $item->estimated_amount }}"
                                                class="w-24 border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500 py-1">
                                            
                                            <div class="text-xs text-gray-400 hidden xl:inline-block mr-2 text-right">
                                                ${{ number_format($item->estimated_amount, 2) }} {{ $item->frequency !== 'monthly' ? '(' . ($freqLabels[$item->frequency] ?? '') . ')' : '' }}
                                            </div>

                                            <select name="frequency"
                                                class="border-gray-300 rounded-md shadow-sm text-xs focus:border-indigo-500 focus:ring-indigo-500 py-1"
                                                onchange="this.form.querySelector('.month-select').disabled = !['annual', 'semiannual'].includes(this.value)">
                                                @foreach($freqLabels as $v => $l)
                                                    <option value="{{ $v }}" {{ $item->frequency === $v ? 'selected' : '' }}>{{ $l }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <input type="number" min="1" max="31" name="payment_day"
                                                value="{{ $item->payment_day }}" placeholder="Día" title="Día de pago"
                                                class="w-16 border-gray-300 rounded-md shadow-sm text-xs focus:border-indigo-500 focus:ring-indigo-500 py-1">
                                            <select name="payment_month" title="Mes de pago"
                                                class="month-select border-gray-300 rounded-md shadow-sm text-xs focus:border-indigo-500 focus:ring-indigo-500 py-1"
                                                {{ in_array($item->frequency, ['annual', 'semiannual']) ? '' : 'disabled' }}>
                                                <option value="">Mes...</option>
                                                @foreach([1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic'] as $m => $name)
                                                    <option value="{{ $m }}" {{ $item->payment_month == $m ? 'selected' : '' }}>
                                                        {{ $name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </form>
                                    
                                    <div class="flex items-center gap-4 border-t border-gray-100 xl:border-t-0 pt-3 xl:pt-0 shrink-0">
                                        <button type="button" onclick="document.getElementById('form-edit-{{ $item->id }}').submit()"
                                            class="text-indigo-600 bg-indigo-50 hover:bg-indigo-100 px-4 py-1.5 rounded-md text-xs font-semibold transition">
                                            Guardar
                                        </button>
                                        <form method="POST" action="{{ route('budgets.delete-item', [$group, $budget, $item]) }}"
                                            data-confirm="Se eliminará el item '{{ $item->concept?->name ?? $item->custom_name ?? $item->category->name }}' del presupuesto."
                                            data-title="¿Eliminar item?" data-btn-text="Sí, eliminar" class="flex items-center">
                                            @csrf @method('DELETE')
                                            <button class="text-rose-400 hover:text-rose-600 px-2 py-1 text-xs font-medium hover:underline" type="button" onclick="window.dispatchEvent(new CustomEvent('open-confirm-modal', {detail: {title: this.closest('form').dataset.title, message: this.closest('form').dataset.confirm, btnText: this.closest('form').dataset.btnText, onConfirm: () => this.closest('form').submit()}}))">Borrar</button>
                                        </form>
                                    </div>
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
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-4 gap-2">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Agregar Nuevo Item</h3>
                        <p class="text-xs text-indigo-600 font-medium flex items-center gap-1 mt-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Se creará un recordatorio automático si estableces un día de pago.
                        </p>
                    </div>
                </div>
                <form method="POST" action="{{ route('budgets.add-item', [$group, $budget]) }}">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <label class="text-xs text-gray-500 font-medium">Categoría</label>
                            <select name="category_id" id="category_select"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required>
                                <option value="">Seleccionar...</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 font-medium">Concepto (Opcional)</label>
                            <select name="concept_id" id="concept_select"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:opacity-50"
                                disabled>
                                <option value="">General de categoría...</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 font-medium">Monto estimado</label>
                            <input type="number" step="0.01" name="estimated_amount"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 font-medium">Frecuencia</label>
                            <select name="frequency" id="frequency_select"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="monthly">Mensual</option>
                                <option value="bimonthly">Bimestral</option>
                                <option value="quarterly">Trimestral</option>
                                <option value="semiannual">Semestral</option>
                                <option value="annual">Anual</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 font-medium">Mes de Pago</label>
                            <select name="payment_month" id="payment_month_select"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:opacity-50"
                                disabled>
                                <option value="">N/A</option>
                                <option value="1">Enero</option>
                                <option value="2">Febrero</option>
                                <option value="3">Marzo</option>
                                <option value="4">Abril</option>
                                <option value="5">Mayo</option>
                                <option value="6">Junio</option>
                                <option value="7">Julio</option>
                                <option value="8">Agosto</option>
                                <option value="9">Septiembre</option>
                                <option value="10">Octubre</option>
                                <option value="11">Noviembre</option>
                                <option value="12">Diciembre</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 font-medium" title="Para auto-agendar recordatorio">Día
                                de Pago (Opc.)</label>
                            <input type="number" min="1" max="31" name="payment_day" placeholder="Ej: 15"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div class="flex items-end col-span-1 sm:col-span-2 lg:col-span-3 border-t pt-3">
                            <button type="submit"
                                class="w-full bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                                + Agregar
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="mt-4">
                <a href="{{ route('budgets.index', $group) }}" class="text-gray-500 hover:text-gray-700 text-sm">←
                    Volver a presupuestos</a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const categorySelect = document.getElementById('category_select');
            const conceptSelect = document.getElementById('concept_select');

            if (categorySelect && conceptSelect) {
                const conceptsJson = @json($concepts->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'category_id' => $c->category_id]));

                categorySelect.addEventListener('change', function () {
                    const categoryId = this.value;
                    conceptSelect.innerHTML = '<option value="">General de categoría...</option>';

                    if (categoryId) {
                        const filteredConcepts = conceptsJson.filter(c => c.category_id == categoryId);
                        filteredConcepts.forEach(c => {
                            conceptSelect.insertAdjacentHTML('beforeend', `<option value="${c.id}">${c.name}</option>`);
                        });
                        conceptSelect.disabled = false;
                    } else {
                        conceptSelect.disabled = true;
                    }
                });
            }

            const frequencySelect = document.getElementById('frequency_select');
            const monthSelect = document.getElementById('payment_month_select');

            if (frequencySelect && monthSelect) {
                frequencySelect.addEventListener('change', function () {
                    if (this.value === 'annual' || this.value === 'semiannual') {
                        monthSelect.disabled = false;
                    } else {
                        monthSelect.disabled = true;
                        monthSelect.value = '';
                    }
                });
            }
        });
    </script>
</x-app-layout>