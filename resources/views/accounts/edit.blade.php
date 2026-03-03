<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800">Detalle de Cuenta: {{ $account->name }}</h2>
            <a href="{{ route('accounts.index', $group) }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">
                &larr; Volver
            </a>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            {{-- Metrics Panel --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Current Balance -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Saldo Actual</p>
                        <p class="text-2xl font-bold {{ $account->current_balance >= 0 ? 'text-gray-900' : 'text-rose-600' }}">
                            ${{ number_format($account->current_balance, 2) }}
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: {{ $account->color }}15">
                        <span class="text-2xl">
                            @switch($account->type)
                                @case('cash') 💵 @break
                                @case('debit') 💳 @break
                                @case('credit') 💎 @break
                                @case('savings') 🏦 @break
                                @case('investment') 📈 @break
                                @case('emergency') 🛡️ @break
                                @default 💰
                            @endswitch
                        </span>
                    </div>
                </div>
                
                <!-- Monthly Income -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-emerald-100 flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Ingresos del Mes</p>
                        <p class="text-2xl font-bold text-emerald-600">
                            ${{ number_format($monthlyIncome ?? 0, 2) }}
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-emerald-50">
                        <span class="text-2xl">📈</span>
                    </div>
                </div>

                <!-- Monthly Expenses -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-rose-100 flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Gastos del Mes</p>
                        <p class="text-2xl font-bold text-rose-600">
                            ${{ number_format($monthlyExpenses ?? 0, 2) }}
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-rose-50">
                        <span class="text-2xl">📉</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Form Section --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <h3 class="font-bold text-gray-800 mb-4">Configuración de Cuenta</h3>
                        @include('accounts.form')
                    </div>
                </div>

                {{-- Transactions Section --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                            <h3 class="font-bold text-gray-800">Últimos Movimientos</h3>
                            <a href="{{ route('transactions.create', $group) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700 whitespace-nowrap">
                                + Nuevo
                            </a>
                        </div>

                        <form method="GET" action="{{ route('accounts.edit', [$group, $account]) }}" class="mb-4" id="accountSearchForm">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input type="text" name="search" id="accountSearchInput" value="{{ request('search') }}"
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-gray-50"
                                    placeholder="Buscar por concepto, descripción o notas...">
                            </div>
                        </form>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const searchInput = document.getElementById('accountSearchInput');
                                if(searchInput) {
                                    let timeout = null;
                                    searchInput.addEventListener('input', function() {
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

                        <div class="space-y-4" x-data="{}">
                            @forelse($transactions as $txn)
                                <div @click="$dispatch('open-transaction-modal', { id: {{ $txn->id }}, groupId: {{ $group->id }} })"
                                     class="flex items-center justify-between p-4 rounded-xl border border-gray-50 bg-gray-50/50 hover:bg-white hover:shadow-sm cursor-pointer transition">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-lg shadow-sm border border-gray-100 shrink-0" style="background-color: {{ $txn->category->color }}15">
                                            @if($txn->category->icon)
                                                <img src="{{ Storage::url($txn->category->icon) }}" class="w-5 h-5 object-contain opacity-80" alt="{{ $txn->category->name }}">
                                            @else
                                                <span style="color: {{ $txn->category->color }}">📋</span>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-semibold text-gray-900 text-sm truncate">
                                                {{ $txn->description ?: ($txn->concept?->name ?: 'Sin descripción') }}
                                            </p>
                                            <div class="flex items-baseline gap-2 mt-0.5">
                                                <span class="text-xs text-gray-500 shrink-0">{{ $txn->date->format('d/m/Y') }}</span>
                                                <span class="text-[10px] font-bold uppercase truncate" style="color: {{ $txn->category->color }}">
                                                    &bull; {{ $txn->category->name }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right shrink-0 ml-4">
                                        <div class="font-bold {{ $txn->type === 'income' ? 'text-emerald-600' : 'text-gray-900' }}">
                                            {{ $txn->type === 'income' ? '+' : '-' }}${{ number_format($txn->amount, 2) }}
                                        </div>
                                        @php
                                            $typeLabels = ['income' => 'Ingreso', 'expense' => 'Gasto', 'transfer' => 'Transferencia', 'savings' => 'Ahorro', 'adjustment' => 'Ajuste'];
                                        @endphp
                                        <div class="text-[10px] text-gray-400 uppercase tracking-wider font-bold mt-1">
                                            {{ $typeLabels[$txn->type] ?? 'Otro' }}
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8 bg-gray-50/50 rounded-xl border border-gray-50">
                                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-white mb-3 shadow-sm border border-gray-100">
                                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                        </svg>
                                    </div>
                                    <p class="text-sm text-gray-500 font-medium">No hay movimientos recientes</p>
                                    <a href="{{ route('transactions.create', $group) }}" class="text-indigo-600 text-xs font-bold hover:underline mt-2 inline-block">Registrar movimiento &rarr;</a>
                                </div>
                            @endforelse
                        </div>

                        @if($transactions->hasPages())
                            <div class="mt-8 flex items-center justify-center gap-4 border-t border-gray-100 pt-6">
                                {{-- Previous Page Link --}}
                                @if ($transactions->onFirstPage())
                                    <span class="p-2 rounded-lg bg-gray-50/50 border border-transparent text-gray-300 cursor-not-allowed">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                    </span>
                                @else
                                    <a href="{{ $transactions->previousPageUrl() }}" class="p-2 rounded-lg bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 hover:text-indigo-600 transition-colors shadow-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                    </a>
                                @endif

                                {{-- Pagination Indicator --}}
                                <span class="text-sm text-gray-600 font-medium px-4">
                                    Página {{ $transactions->currentPage() }} de {{ $transactions->lastPage() }}
                                </span>

                                {{-- Next Page Link --}}
                                @if ($transactions->hasMorePages())
                                    <a href="{{ $transactions->nextPageUrl() }}" class="p-2 rounded-lg bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 hover:text-indigo-600 transition-colors shadow-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </a>
                                @else
                                    <span class="p-2 rounded-lg bg-gray-50/50 border border-transparent text-gray-300 cursor-not-allowed">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>