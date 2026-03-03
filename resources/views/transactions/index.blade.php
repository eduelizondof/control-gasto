<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800">Movimientos</h2>
            <a href="{{ route('transactions.create', $group) }}"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition shadow-sm">
                + Nuevo Movimiento
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 mb-6">
                <form method="GET" action="{{ route('transactions.index', $group) }}">
                    <div class="mb-4">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" name="search" id="txnSearchInput" value="{{ request('search') }}"
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="Buscar por concepto, descripción o notas...">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                        <div>
                            <label class="text-xs text-gray-500 font-medium">Tipo</label>
                            <select name="type"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todos</option>
                                @foreach(['income' => 'Ingreso', 'expense' => 'Gasto', 'transfer' => 'Transferencia', 'savings' => 'Ahorro'] as $val => $label)
                                    <option value="{{ $val }}" {{ request('type') === $val ? 'selected' : '' }}>{{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 font-medium">Categoría</label>
                            <select name="category_id"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todas</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 font-medium">Desde</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 font-medium">Hasta</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition w-full">Filtrar</button>
                            <a href="{{ route('transactions.index', $group) }}"
                                class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-lg text-sm border border-gray-200 hover:bg-gray-50 transition">Limpiar</a>
                        </div>
                </form>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const searchInput = document.getElementById('txnSearchInput');
                    if (searchInput) {
                        let timeout = null;
                        searchInput.addEventListener('input', function () {
                            clearTimeout(timeout);
                            timeout = setTimeout(() => {
                                this.form.submit();
                            }, 500);
                        });

                        if (searchInput.value) {
                            const len = searchInput.value.length;
                            setTimeout(() => {
                                searchInput.focus();
                                searchInput.setSelectionRange(len, len);
                            }, 10);
                        }
                    }
                });
            </script>

            <!-- Transactions List -->
            <div class="space-y-4">
                @forelse($transactions as $txn)
                    <div
                        class="bg-white hover:bg-gray-50/50 rounded-2xl shadow-sm border border-gray-100 p-5 transition flex flex-col md:flex-row md:items-center gap-4 md:gap-6">
                        <!-- Date & Description -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3 mb-2">
                                <span
                                    class="text-sm font-medium text-gray-500 whitespace-nowrap">{{ $txn->date->format('d/m/Y') }}</span>
                                @php
                                    $typeLabels = ['income' => ['Ingreso', 'bg-emerald-100 text-emerald-700'], 'expense' => ['Gasto', 'bg-rose-100 text-rose-700'], 'transfer' => ['Transferencia', 'bg-blue-100 text-blue-700'], 'savings' => ['Ahorro', 'bg-cyan-100 text-cyan-700'], 'adjustment' => ['Ajuste', 'bg-gray-100 text-gray-700']];
                                    $tl = $typeLabels[$txn->type] ?? ['Otro', 'bg-gray-100 text-gray-500'];
                                @endphp
                                <span
                                    class="px-2 py-0.5 rounded-md text-[10px] uppercase tracking-wider font-bold {{ $tl[1] }}">{{ $tl[0] }}</span>
                            </div>
                            <h3 class="text-base font-semibold text-gray-900 truncate">
                                {{ $txn->description ?: ($txn->concept?->name ?: '-') }}
                            </h3>
                            <div class="flex flex-wrap items-center gap-2 mt-2">
                                <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-xs font-medium"
                                    style="background-color: {{ $txn->category->color }}15; color: {{ $txn->category->color }}">
                                    <span class="w-1.5 h-1.5 rounded-full"
                                        style="background-color: {{ $txn->category->color }}"></span>
                                    {{ $txn->category->name }}
                                </span>
                                <span
                                    class="text-xs text-gray-600 bg-gray-100 px-2 py-1 rounded-md">{{ $txn->sourceAccount->name }}</span>
                            </div>
                        </div>

                        <!-- Amount & Actions -->
                        <div class="flex items-center justify-between md:flex-col md:items-end gap-3 mt-2 md:mt-0">
                            <div
                                class="text-lg font-bold whitespace-nowrap {{ $txn->type === 'income' ? 'text-emerald-600' : 'text-gray-900' }}">
                                {{ $txn->type === 'income' ? '+' : '-' }}${{ number_format($txn->amount, 2) }}
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('transactions.edit', [$group, $txn]) }}"
                                    class="px-3 py-1.5 text-xs font-bold text-indigo-600 bg-transparent border border-indigo-200 rounded-lg hover:bg-indigo-50 hover:border-indigo-300 transition-colors">
                                    Editar
                                </a>
                                <form method="POST" action="{{ route('transactions.destroy', [$group, $txn]) }}"
                                    data-confirm="Se eliminará el movimiento '{{ $txn->description ?: ($txn->concept?->name ?: 'sin descripción') }}' por ${{ number_format($txn->amount, 2) }}."
                                    data-title="¿Eliminar movimiento?" data-btn-text="Sí, eliminar">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="px-3 py-1.5 text-xs font-bold text-rose-600 bg-transparent border border-rose-200 rounded-lg hover:bg-rose-50 hover:border-rose-300 transition-colors">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
                            <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-medium text-gray-900">No hay movimientos</h3>
                        <p class="mt-1 text-sm text-gray-500">Aún no has registrado ningún movimiento con estos filtros.
                        </p>
                        <a href="{{ route('transactions.create', $group) }}"
                            class="inline-flex items-center justify-center mt-4 px-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition">
                            Crear movimiento →
                        </a>
                    </div>
                @endforelse
            </div>

            @if($transactions->hasPages())
                <div class="mt-8 flex items-center justify-center gap-4">
                    {{-- Previous Page Link --}}
                    @if ($transactions->onFirstPage())
                        <span class="p-2 rounded-lg bg-gray-50/50 border border-transparent text-gray-300 cursor-not-allowed">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                                </path>
                            </svg>
                        </span>
                    @else
                        <a href="{{ $transactions->previousPageUrl() }}"
                            class="p-2 rounded-lg bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 hover:text-indigo-600 transition-colors shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                                </path>
                            </svg>
                        </a>
                    @endif

                    {{-- Pagination Indicator --}}
                    <span class="text-sm text-gray-600 font-medium px-4">
                        Página {{ $transactions->currentPage() }} de {{ $transactions->lastPage() }}
                    </span>

                    {{-- Next Page Link --}}
                    @if ($transactions->hasMorePages())
                        <a href="{{ $transactions->nextPageUrl() }}"
                            class="p-2 rounded-lg bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 hover:text-indigo-600 transition-colors shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                        </a>
                    @else
                        <span class="p-2 rounded-lg bg-gray-50/50 border border-transparent text-gray-300 cursor-not-allowed">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                        </span>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>