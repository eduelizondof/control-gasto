<div class="mt-8 bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
        Calendario de Pagos de Quincena
    </h3>

    @if (session()->has('success'))
        <div class="mb-4 p-3 bg-emerald-50 border border-emerald-100 text-emerald-600 rounded-xl text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
        <!-- Lista de calendarios existentes -->
        <div class="xl:col-span-1 space-y-4">
            <h4 class="font-bold text-gray-700 text-sm uppercase tracking-wider mb-2">Personas Configuradas</h4>

            @forelse($this->existingCalendars as $person => $entries)
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <div class="font-bold text-gray-800">{{ $person }}</div>
                            <div class="text-xs text-gray-500">{{ $entries->count() }} quincenas programadas</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button wire:click="editPerson('{{ $person }}')"
                                class="px-2 py-1.5 text-xs font-bold text-indigo-600 bg-transparent border border-indigo-200 rounded-lg hover:bg-indigo-50 hover:border-indigo-300 transition-colors"
                                title="Editar">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                            </button>
                            <button wire:click="duplicatePerson('{{ $person }}')"
                                class="px-2 py-1.5 text-xs font-bold text-emerald-600 bg-transparent border border-emerald-200 rounded-lg hover:bg-emerald-50 hover:border-emerald-300 transition-colors"
                                title="Duplicar">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2">
                                    </path>
                                </svg>
                            </button>
                            <button type="button" x-data x-on:click="Swal.fire({
                                                                    title: '¿Eliminar calendario?',
                                                                    text: '¿Seguro que deseas eliminar todas las quincenas de {{ $person }}?',
                                                                    icon: 'warning',
                                                                    showCancelButton: true,
                                                                    confirmButtonColor: '#ef4444',
                                                                    cancelButtonColor: '#6b7280',
                                                                    confirmButtonText: 'Sí, eliminar',
                                                                    cancelButtonText: 'Cancelar'
                                                                }).then((result) => {
                                                                    if(result.isConfirmed) {
                                                                        $wire.deletePerson('{{ $person }}');
                                                                    }
                                                                })"
                                class="px-2 py-1.5 text-xs font-bold text-rose-600 bg-transparent border border-rose-200 rounded-lg hover:bg-rose-50 hover:border-rose-300 transition-colors"
                                title="Eliminar">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">No hay calendarios configurados aún.</p>
            @endforelse

            <button wire:click="resetForm"
                class="w-full mt-4 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 px-4 py-2 rounded-xl text-sm font-semibold transition">
                + Nueva Persona
            </button>
        </div>

        <!-- Formulario principal -->
        <div class="xl:col-span-3">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <h4 class="font-bold text-gray-800 mb-4">
                    {{ $editingPerson ? 'Editar: ' . $editingPerson : 'Registrar Nuevo Calendario' }}
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6" x-data="paymentCalendarManager">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre (Usuario del Grupo)</label>
                        <select wire:model.defer="personName"
                            class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">Selecciona persona...</option>
                            @foreach($this->groupUsers as $user)
                                <option value="{{ $user->name }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('personName') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Cuenta de Depósito</label>
                        <select wire:model.defer="accountId"
                            class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">Selecciona cuenta...</option>
                            @foreach($this->accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->name }} ({{ $account->type_labels }})
                                </option>
                            @endforeach
                        </select>
                        @error('accountId') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Categoría General</label>
                        <div wire:ignore>
                            <select id="calendar_category_id" class="w-full text-sm">
                                <option value="">Selecciona...</option>
                                @foreach($this->categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('categoryId') <span class="text-xs text-rose-500 block mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Concepto General
                            (Opcional)</label>
                        <div wire:ignore>
                            <select id="calendar_concept_id" class="w-full text-sm">
                                <option value="">Ninguno...</option>
                                @foreach($this->concepts as $concept)
                                    <option value="{{ $concept->id }}">{{ $concept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('conceptId') <span class="text-xs text-rose-500 block mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                @if(!$isEditing && count($calendarEntries) === 0)
                    <div class="bg-indigo-50 rounded-xl p-5 border border-indigo-100">
                        <h5 class="text-sm font-bold text-indigo-900 mb-2">Generación Automática</h5>
                        <p class="text-xs text-indigo-800 mb-4">Puedes generar las 24 quincenas del año precalculadas para
                            los días 15 y fin de mes (recorridos a viernes si caen en fin de semana). Ingresa el monto para
                            todas.</p>

                        <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
                            <div class="w-full sm:w-64">
                                <label class="block text-xs font-semibold text-indigo-900 mb-1">Monto por Quincena</label>
                                <input type="number" step="0.01" wire:model.defer="defaultAmount" placeholder="Ej. 15000"
                                    class="w-full rounded-lg border-indigo-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                @error('defaultAmount') <span class="text-xs text-rose-500 block mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <button wire:click="generateDefaults"
                                class="w-full sm:w-auto mt-0 sm:mt-5 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition shadow-sm whitespace-nowrap">
                                Generar 24 Quincenas
                            </button>
                        </div>
                    </div>
                @else
                    <div class="space-y-3 max-h-[500px] overflow-y-auto pr-2 mb-4">
                        @foreach($calendarEntries as $index => $entry)
                            <div class="grid grid-cols-12 gap-3 p-3 bg-gray-50 rounded-xl border border-gray-200 items-end"
                                wire:key="entry-{{$index}}">
                                <div class="col-span-12 sm:col-span-4 lg:col-span-3">
                                    <label class="block text-xs font-semibold text-gray-500 mb-1">Fecha</label>
                                    <input type="date" wire:model.defer="calendarEntries.{{ $index }}.payment_date"
                                        class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    @error("calendarEntries.{$index}.payment_date") <span
                                    class="text-xs text-rose-500 block mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-span-12 sm:col-span-8 lg:col-span-5">
                                    <label class="block text-xs font-semibold text-gray-500 mb-1">Descripción</label>
                                    <input type="text" wire:model.defer="calendarEntries.{{ $index }}.concept"
                                        placeholder="Concepto (Ej. Quincena 1 Ene)"
                                        class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    @error("calendarEntries.{$index}.concept") <span
                                    class="text-xs text-rose-500 block mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-span-10 sm:col-span-9 lg:col-span-3">
                                    <label class="block text-xs font-semibold text-gray-500 mb-1">Monto</label>
                                    <input type="number" step="0.01" wire:model.defer="calendarEntries.{{ $index }}.amount"
                                        placeholder="Monto"
                                        class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    @error("calendarEntries.{$index}.amount") <span
                                    class="text-xs text-rose-500 block mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-span-2 sm:col-span-3 lg:col-span-1 flex items-end justify-end">
                                    <button type="button" wire:click="removeEntry({{ $index }})"
                                        class="px-3 py-2 text-xs font-bold text-rose-600 bg-transparent border border-rose-200 rounded-lg hover:bg-rose-50 hover:border-rose-300 transition-colors"
                                        title="Eliminar Quincena">
                                        <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div
                        class="flex flex-col sm:flex-row justify-between items-start sm:items-center pt-5 border-t border-gray-100 gap-4">
                        <button wire:click="addEntry"
                            class="text-indigo-600 hover:text-indigo-800 text-sm font-semibold flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Agregar Quincena
                        </button>

                        <div class="flex gap-2 w-full sm:w-auto justify-end">
                            <button wire:click="resetForm"
                                class="px-3 py-2 text-sm font-bold text-gray-600 bg-transparent border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 transition-colors w-full sm:w-auto text-center">
                                Cancelar
                            </button>
                            <button wire:click="saveCalendar"
                                class="px-3 py-2 text-sm font-bold text-white bg-indigo-600 border border-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors shadow-sm w-full sm:w-auto text-center">
                                Guardar Calendario
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <style>
            .select2-container--default .select2-selection--single {
                border-color: #e5e7eb !important;
                border-radius: 0.75rem !important;
                height: 38px !important;
                display: flex !important;
                align-items: center !important;
                font-size: 0.875rem !important;
            }

            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 36px !important;
            }

            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: 36px !important;
                color: #374151 !important;
            }

            .select2-container--default .select2-results__option--highlighted[aria-selected] {
                background-color: #4f46e5 !important;
            }

            .select2-dropdown {
                border-color: #e5e7eb !important;
                border-radius: 0.75rem !important;
                box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1) !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('paymentCalendarManager', () => ({
                    categoryId: @entangle('categoryId'),
                    conceptId: @entangle('conceptId'),
                    allConcepts: @json($this->concepts->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'category_id' => $c->category_id])),

                    init() {
                        let catSelect = $('#calendar_category_id');
                        let conSelect = $('#calendar_concept_id');

                        // Initialize Select2
                        catSelect.select2({ placeholder: 'Selecciona...', width: '100%' });
                        conSelect.select2({ placeholder: 'Ninguno...', width: '100%', allowClear: true });

                        // Handle Changes
                        catSelect.on('change', (e) => {
                            this.categoryId = e.target.value;
                            this.filterConcepts(e.target.value);
                        });

                        conSelect.on('change', (e) => {
                            this.conceptId = e.target.value;
                        });

                        // Watches
                        this.$watch('categoryId', (value) => {
                            if (catSelect.val() != value) {
                                catSelect.val(value).trigger('change.select2');
                                this.filterConcepts(value);
                            }
                        });

                        this.$watch('conceptId', (value) => {
                            if (conSelect.val() != value) {
                                conSelect.val(value).trigger('change.select2');
                            }
                        });

                        // Initial filter
                        if (this.categoryId) {
                            this.filterConcepts(this.categoryId);
                        }
                    },

                    filterConcepts(catId) {
                        let conSelect = $('#calendar_concept_id');
                        let current = this.conceptId;

                        conSelect.empty().append(new Option('Ninguno...', '', false, false));

                        if (catId) {
                            const filtered = this.allConcepts.filter(c => c.category_id == catId);
                            filtered.forEach(c => {
                                conSelect.append(new Option(c.name, c.id, false, false));
                            });
                        }

                        if (current) {
                            conSelect.val(current);
                        }
                        conSelect.trigger('change.select2');
                    }
                }));
            });
        </script>
    @endpush