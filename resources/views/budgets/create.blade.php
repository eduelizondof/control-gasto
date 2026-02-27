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

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <x-input-label for="name" value="Nombre del presupuesto" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                :value="old('name', $budget->name ?? '')" required
                                placeholder="Ej: Presupuesto Base 2025" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
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
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-bold text-gray-800">Items del Presupuesto</h3>
                                <button type="button" id="add-item-btn"
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                                    + Agregar Item
                                </button>
                            </div>

                            <div id="items-container">
                                <div class="budget-item bg-gray-50 rounded-xl p-4 mb-3" data-index="0">
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                        <div>
                                            <label class="text-xs text-gray-500 font-medium">Categoría</label>
                                            <select name="items[0][category_id]"
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                required>
                                                <option value="">Seleccionar...</option>
                                                @foreach($categories as $cat)
                                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="text-xs text-gray-500 font-medium">Nombre / Concepto</label>
                                            <input type="text" name="items[0][custom_name]"
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                placeholder="Ej: Renta" required>
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
                                    </div>
                                    <div class="mt-2 flex justify-end">
                                        <button type="button"
                                            class="remove-item text-rose-500 text-xs hover:underline hidden">Eliminar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-end gap-3 mt-8">
                        <a href="{{ route('budgets.index', $group) }}"
                            class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium text-sm">Cancelar</a>
                        <x-primary-button>{{ isset($budget) ? 'Actualizar' : 'Crear' }} Presupuesto</x-primary-button>
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

                addBtn.addEventListener('click', function () {
                    const catOptions = categoriesJson.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
                    const html = `
                        <div class="budget-item bg-gray-50 rounded-xl p-4 mb-3" data-index="${itemIndex}">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="text-xs text-gray-500 font-medium">Categoría</label>
                                    <select name="items[${itemIndex}][category_id]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500" required><option value="">Seleccionar...</option>${catOptions}</select>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 font-medium">Nombre / Concepto</label>
                                    <input type="text" name="items[${itemIndex}][custom_name]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
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
                            </div>
                            <div class="mt-2 flex justify-end">
                                <button type="button" class="remove-item text-rose-500 text-xs hover:underline">Eliminar</button>
                            </div>
                        </div>`;
                    container.insertAdjacentHTML('beforeend', html);
                    itemIndex++;
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