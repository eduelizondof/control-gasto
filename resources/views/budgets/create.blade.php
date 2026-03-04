<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800">{{ isset($budget) ? 'Editar Presupuesto' : 'Nuevo Presupuesto' }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                <form method="POST"
                    action="{{ isset($budget) ? route('budgets.update', [$group, $budget]) : route('budgets.store', $group) }}">
                    @csrf
                    @if(isset($budget)) @method('PUT') @endif

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div>
                            <x-input-label for="name" value="Nombre del presupuesto" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                :value="old('name', $budget->name ?? '')" required
                                placeholder="Ej: Presupuesto Base 2025" />
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

                        <div class="flex items-end">
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="is_active" value="1"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" {{ old('is_active', $budget->is_active ?? true) ? 'checked' : '' }}>
                                <span class="text-sm text-gray-600">Presupuesto activo</span>
                            </label>
                        </div>
                    </div>

                    @if(!isset($budget))
                        {{-- Budget Items (only on create) --}}
                        <div class="border-t border-gray-100 pt-6">
                            <div class="flex flex-col md:flex-row md:items-center justify-between mb-4 gap-2">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800">Items del Presupuesto</h3>
                                    <p class="text-xs text-indigo-600 font-medium flex items-center gap-1 mt-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Se creará un recordatorio automático si estableces un día de pago.
                                    </p>
                                </div>
                                <button type="button" id="add-item-btn"
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition shrink-0">
                                    + Agregar Item
                                </button>
                            </div>

                            <div id="items-container">
                                <div class="budget-item bg-gray-50 rounded-xl p-4 mb-3" data-index="0">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                        <div>
                                            <label class="text-xs text-gray-500 font-medium">Categoría</label>
                                            <select name="items[0][category_id]"
                                                class="category-select mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                required>
                                                <option value="">Seleccionar...</option>
                                                @foreach($categories as $cat)
                                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="text-xs text-gray-500 font-medium">Concepto (Opcional)</label>
                                            <select name="items[0][concept_id]"
                                                class="concept-select mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:opacity-50"
                                                disabled>
                                                <option value="">General de categoría...</option>
                                                {{-- Opciones por JS --}}
                                            </select>
                                        </div>
                                        <div>
                                            <label class="text-xs text-gray-500 font-medium">Monto estimado</label>
                                            <input type="number" step="0.01" name="items[0][estimated_amount]"
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                required>
                                        </div>
                                        <div>
                                            <label class="text-xs text-gray-500 font-medium">Frecuencia</label>
                                            <select name="items[0][frequency]"
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
                                            <select name="items[0][payment_month]"
                                                class="payment-month-select mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:opacity-50"
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
                                            <label class="text-xs text-gray-500 font-medium"
                                                title="Para auto-agendar recordatorio">Día de Pago (Opc.)</label>
                                            <input type="number" min="1" max="31" name="items[0][payment_day]"
                                                placeholder="Ej: 15"
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        </div>
                                        <div
                                            class="mt-2 flex items-end justify-end col-span-1 sm:col-span-2 lg:col-span-3 border-t pt-3">
                                            <button type="button"
                                                class="remove-item text-rose-500 text-xs font-semibold hover:text-rose-700 hover:underline hidden">❌
                                                Eliminar Item</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    @endif

                        <div class="flex justify-end gap-3 mt-8">
                            <a href="{{ route('budgets.index', $group) }}"
                                class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium text-sm">Cancelar</a>
                            <x-primary-button>{{ isset($budget) ? 'Actualizar' : 'Crear' }}
                                Presupuesto</x-primary-button>
                        </div>
                </form>
            </div>
        </div>
    </div>

    @if(!isset($budget))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                let itemIndex = 1;
                const container = document.getElementById('items-container');
                const addBtn = document.getElementById('add-item-btn');
                const categoriesJson = @json($categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name]));

                const conceptsJson = @json($concepts->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'category_id' => $c->category_id]));

                addBtn.addEventListener('click', function () {
                    const catOptions = categoriesJson.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
                    const html = `
                                                <div class="budget-item bg-gray-50 rounded-xl p-4 mb-3" data-index="${itemIndex}">
                                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                                        <div>
                                                            <label class="text-xs text-gray-500 font-medium">Categoría</label>
                                                            <select name="items[${itemIndex}][category_id]" class="category-select mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500" required><option value="">Seleccionar...</option>${catOptions}</select>
                                                        </div>
                                                        <div>
                                                            <label class="text-xs text-gray-500 font-medium">Concepto (Opcional)</label>
                                                            <select name="items[${itemIndex}][concept_id]" class="concept-select mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:opacity-50" disabled>
                                                                <option value="">General de categoría...</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label class="text-xs text-gray-500 font-medium">Monto estimado</label>
                                                            <input type="number" step="0.01" name="items[${itemIndex}][estimated_amount]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                                        </div>
                                                        <div>
                                                            <label class="text-xs text-gray-500 font-medium">Frecuencia</label>
                                                            <select name="items[${itemIndex}][frequency]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                                <option value="monthly">Mensual</option><option value="bimonthly">Bimestral</option><option value="quarterly">Trimestral</option><option value="semiannual">Semestral</option><option value="annual">Anual</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label class="text-xs text-gray-500 font-medium">Mes de Pago</label>
                                                            <select name="items[${itemIndex}][payment_month]" class="payment-month-select mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:opacity-50" disabled>
                                                                <option value="">N/A</option><option value="1">Enero</option><option value="2">Febrero</option><option value="3">Marzo</option><option value="4">Abril</option><option value="5">Mayo</option><option value="6">Junio</option><option value="7">Julio</option><option value="8">Agosto</option><option value="9">Septiembre</option><option value="10">Octubre</option><option value="11">Noviembre</option><option value="12">Diciembre</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label class="text-xs text-gray-500 font-medium" title="Para auto-agendar recordatorio">Día de Pago (Opc.)</label>
                                                            <input type="number" min="1" max="31" name="items[${itemIndex}][payment_day]" placeholder="Ej: 15" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                        </div>
                                                    </div>
                                                    <div class="mt-2 flex items-end justify-end col-span-1 border-t sm:col-span-2 lg:col-span-3 pt-3">
                                                        <button type="button" class="remove-item text-rose-500 text-xs font-semibold hover:text-rose-700 hover:underline">❌ Eliminar Item</button>
                                                    </div>
                                                </div>`;
                    container.insertAdjacentHTML('beforeend', html);
                    itemIndex++;
                });

                container.addEventListener('change', function (e) {
                    if (e.target.classList.contains('category-select')) {
                        const row = e.target.closest('.budget-item');
                        const conceptSelect = row.querySelector('.concept-select');
                        const categoryId = e.target.value;

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
                    } else if (e.target.name && e.target.name.includes('[frequency]')) {
                        const row = e.target.closest('.budget-item');
                        const monthSelect = row.querySelector('.payment-month-select');
                        if (e.target.value === 'annual' || e.target.value === 'semiannual') {
                            monthSelect.disabled = false;
                        } else {
                            monthSelect.disabled = true;
                            monthSelect.value = '';
                        }
                    }
                });

                container.addEventListener('click', function (e) {
                    if (e.target.classList.contains('remove-item')) {
                        e.target.closest('.budget-item').remove();
                    }
                });
            });
        </script>
    @endif
</x-app-layout>