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
                <form method="GET" action="{{ route('transactions.index', $group) }}"
                    class="grid grid-cols-2 md:grid-cols-5 gap-4">
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

            <!-- Transactions Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Fecha</th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">
                                    Descripción</th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Categoría
                                </th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Cuenta
                                </th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Tipo</th>
                                <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Monto
                                </th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($transactions as $txn)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-6 py-4 text-gray-600 whitespace-nowrap">{{ $txn->date->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-800">
                                            {{ $txn->description ?: ($txn->concept?->name ?: '-') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium"
                                            style="background-color: {{ $txn->category->color }}15; color: {{ $txn->category->color }}">
                                            <span class="w-2 h-2 rounded-full"
                                                style="background-color: {{ $txn->category->color }}"></span>
                                            {{ $txn->category->name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">{{ $txn->sourceAccount->name }}</td>
                                    <td class="px-6 py-4">
                                        @php
                                            $typeLabels = ['income' => ['Ingreso', 'bg-emerald-100 text-emerald-700'], 'expense' => ['Gasto', 'bg-rose-100 text-rose-700'], 'transfer' => ['Transferencia', 'bg-blue-100 text-blue-700'], 'savings' => ['Ahorro', 'bg-cyan-100 text-cyan-700'], 'adjustment' => ['Ajuste', 'bg-gray-100 text-gray-700']];
                                            $tl = $typeLabels[$txn->type] ?? ['Otro', 'bg-gray-100 text-gray-500'];
                                        @endphp
                                        <span
                                            class="px-2.5 py-1 rounded-lg text-xs font-medium {{ $tl[1] }}">{{ $tl[0] }}</span>
                                    </td>
                                    <td
                                        class="px-6 py-4 text-right font-bold whitespace-nowrap {{ $txn->type === 'income' ? 'text-emerald-600' : 'text-gray-800' }}">
                                        {{ $txn->type === 'income' ? '+' : '-' }}${{ number_format($txn->amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('transactions.edit', [$group, $txn]) }}"
                                                class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">Editar</a>
                                            <form method="POST" action="{{ route('transactions.destroy', [$group, $txn]) }}"
                                                data-confirm="Se eliminará el movimiento '{{ $txn->description ?: ($txn->concept?->name ?: 'sin descripción') }}' por ${{ number_format($txn->amount, 2) }}."
                                                data-title="¿Eliminar movimiento?" data-btn-text="Sí, eliminar">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="text-rose-500 hover:text-rose-700 text-xs font-medium">Eliminar</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                        No hay movimientos registrados.
                                        <a href="{{ route('transactions.create', $group) }}"
                                            class="text-indigo-600 font-medium hover:underline ml-1">Crear uno →</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($transactions->hasPages())
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>