<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 leading-tight">Panel de Control</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $group->name }} — {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</p>
            </div>
            <a href="{{ route('transactions.create', $group) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition shadow-sm">
                + Nuevo Movimiento
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Pending Invitations Alert --}}
            @if($pendingInvitationsCount > 0)
                <div class="mb-6 bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-200 rounded-2xl p-4 flex items-center justify-between"
                    x-data="{ show: true }" x-show="show" x-transition>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-indigo-800">
                                Tienes {{ $pendingInvitationsCount }} invitación{{ $pendingInvitationsCount > 1 ? 'es' : '' }} pendiente{{ $pendingInvitationsCount > 1 ? 's' : '' }}
                            </p>
                            <p class="text-xs text-indigo-600">Te han invitado a unirte a un grupo</p>
                        </div>
                    </div>
                    <a href="{{ route('invitations.index') }}"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition shadow-sm shrink-0">
                        Ver invitaciones
                    </a>
                </div>
            @endif
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Balance -->
                <div class="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-2xl p-6 text-white shadow-lg shadow-indigo-600/20">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-indigo-200 text-sm font-medium">Saldo Total</span>
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                    <div class="text-3xl font-black">${{ number_format($totalBalance, 2) }}</div>
                </div>

                <!-- Monthly Income -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-gray-500 text-sm font-medium">Ingresos del Mes</span>
                        <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path></svg>
                        </div>
                    </div>
                    <div class="text-3xl font-black text-gray-900">${{ number_format($monthlyIncome, 2) }}</div>
                </div>

                <!-- Monthly Expenses -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-gray-500 text-sm font-medium">Gastos del Mes</span>
                        <div class="w-10 h-10 bg-rose-100 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"></path></svg>
                        </div>
                    </div>
                    <div class="text-3xl font-black text-gray-900">${{ number_format($monthlyExpenses, 2) }}</div>
                </div>

                <!-- Savings -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-gray-500 text-sm font-medium">Ahorro del Mes</span>
                        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                        </div>
                    </div>
                    <div class="text-3xl font-black text-gray-900">${{ number_format($monthlySavings, 2) }}</div>
                </div>
            </div>

            <!-- Budget vs Expenses + Accounts -->
            <div class="grid lg:grid-cols-3 gap-6 mb-8">
                <!-- Budget Overview -->
                <div class="lg:col-span-1 bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Presupuesto vs Gasto Real</h3>
                    @if($activeBudget)
                        @php
                            $budgetDiff = $budgetTotal - $monthlyExpenses;
                            $budgetPercent = $budgetTotal > 0 ? min(100, round(($monthlyExpenses / $budgetTotal) * 100)) : 0;
                        @endphp
                        {{-- Global bar --}}
                        <div class="mb-5">
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-600">Gastado: ${{ number_format($monthlyExpenses, 2) }}</span>
                                <span class="text-gray-600">de ${{ number_format($budgetTotal, 2) }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-4 overflow-hidden">
                                <div class="h-4 rounded-full transition-all duration-500 {{ $budgetPercent > 90 ? 'bg-rose-500' : ($budgetPercent > 70 ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width: {{ $budgetPercent }}%"></div>
                            </div>
                            <p class="text-sm mt-3 {{ $budgetDiff >= 0 ? 'text-emerald-600' : 'text-rose-600' }} font-semibold">
                                {{ $budgetDiff >= 0 ? 'Disponible: $' . number_format($budgetDiff, 2) : 'Excedido: $' . number_format(abs($budgetDiff), 2) }}
                            </p>
                        </div>

                        {{-- Per-concept breakdown --}}
                        @if($budgetBreakdown->isNotEmpty())
                            <div class="border-t border-gray-100 pt-4">
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Desglose por concepto</p>
                                <div class="space-y-3 max-h-64 overflow-y-auto pr-1">
                                    @foreach($budgetBreakdown as $entry)
                                        <div>
                                            <div class="flex items-center justify-between text-xs mb-1">
                                                <div class="flex items-center gap-1.5 min-w-0">
                                                    <span class="w-2 h-2 rounded-full shrink-0" style="background-color: {{ $entry->category->color }}"></span>
                                                    <span class="font-medium text-gray-700 truncate">{{ $entry->name }}</span>
                                                </div>
                                                <span class="font-bold shrink-0 ml-2 {{ $entry->diff >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                                    {{ $entry->diff >= 0 ? '-$' . number_format($entry->diff, 2) : '+$' . number_format(abs($entry->diff), 2) }}
                                                </span>
                                            </div>
                                            <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                                <div class="h-1.5 rounded-full transition-all duration-300 {{ $entry->percent > 90 ? 'bg-rose-400' : ($entry->percent > 70 ? 'bg-amber-400' : 'bg-emerald-400') }}" style="width: {{ $entry->percent }}%"></div>
                                            </div>
                                            <div class="flex justify-between text-[10px] text-gray-400 mt-0.5">
                                                <span>${{ number_format($entry->spent, 2) }} gastado</span>
                                                <span>${{ number_format($entry->budgeted, 2) }} presup.</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @else
                        <p class="text-gray-400 text-sm">No hay presupuesto activo.</p>
                        <a href="{{ route('budgets.create', $group) }}" class="text-indigo-600 text-sm font-medium hover:underline">Crear presupuesto →</a>
                    @endif
                </div>

                <!-- Accounts -->
                <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Mis Cuentas</h3>
                        <a href="{{ route('accounts.index', $group) }}" class="text-indigo-600 text-sm font-medium hover:underline">Ver todas →</a>
                    </div>
                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($accounts as $account)
                            <div class="rounded-xl p-4 border border-gray-100 hover:border-indigo-200 transition">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: {{ $account->color }}20">
                                        <span class="text-lg">
                                            @switch($account->type)
                                                @case('cash') 💵 @break
                                                @case('debit') 💳 @break
                                                @case('credit') 💎 @break
                                                @case('savings') 🏦 @break
                                                @case('investment') 📈 @break
                                                @default 💰
                                            @endswitch
                                        </span>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-800 text-sm">{{ $account->name }}</div>
                                        <div class="text-gray-400 text-xs">{{ $account->type_labels }}</div>
                                    </div>
                                </div>
                                <div class="text-xl font-black {{ $account->current_balance >= 0 ? 'text-gray-900' : 'text-rose-600' }}">
                                    ${{ number_format($account->current_balance, 2) }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Recent Transactions + Upcoming Reminders -->
            <div class="grid lg:grid-cols-3 gap-6 mb-8">
                <!-- Recent Transactions -->
                <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Últimos Movimientos</h3>
                        <a href="{{ route('transactions.index', $group) }}" class="text-indigo-600 text-sm font-medium hover:underline">Ver todos →</a>
                    </div>
                    <div class="space-y-3">
                        @forelse($recentTransactions as $txn)
                            <div class="flex items-center justify-between py-2 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: {{ $txn->category->color }}15">
                                        <div class="w-3 h-3 rounded-full" style="background-color: {{ $txn->category->color }}"></div>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-800 text-sm">{{ $txn->description ?: $txn->concept?->name ?: $txn->category->name }}</div>
                                        <div class="text-gray-400 text-xs">{{ $txn->date->translatedFormat('d M') }} · {{ $txn->sourceAccount->name }}</div>
                                    </div>
                                </div>
                                <span class="font-bold text-sm {{ in_array($txn->type, ['income']) ? 'text-emerald-600' : 'text-gray-800' }}">
                                    {{ in_array($txn->type, ['income']) ? '+' : '-' }}${{ number_format($txn->amount, 2) }}
                                </span>
                            </div>
                        @empty
                            <p class="text-gray-400 text-sm py-4 text-center">No hay movimientos este mes.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Upcoming Reminders -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Próximos Pagos</h3>
                        <a href="{{ route('reminders.index', $group) }}" class="text-indigo-600 text-sm font-medium hover:underline">Ver todos →</a>
                    </div>
                    <div class="space-y-4">
                        @forelse($upcomingReminders as $reminder)
                            <div class="flex items-center gap-3 p-3 rounded-xl bg-amber-50/50 border border-amber-100/50">
                                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="font-medium text-gray-800 text-sm truncate">{{ $reminder->name }}</div>
                                    <div class="text-gray-400 text-xs">{{ $reminder->next_date?->translatedFormat('d M Y') }}</div>
                                    @if($reminder->estimated_amount)
                                        <div class="text-amber-600 text-xs font-semibold">${{ number_format($reminder->estimated_amount, 2) }}</div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-400 text-sm py-4 text-center">Sin pagos próximos.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Active Debts -->
            @if($activeDebts->isNotEmpty())
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Deudas Activas</h3>
                        <a href="{{ route('debts.index', $group) }}" class="text-indigo-600 text-sm font-medium hover:underline">Ver todas →</a>
                    </div>
                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($activeDebts as $debt)
                            <div class="rounded-xl p-4 border border-gray-100">
                                <div class="font-semibold text-gray-800 text-sm mb-2">{{ $debt->name }}</div>
                                <div class="flex justify-between text-xs text-gray-500 mb-2">
                                    <span>Pagado: ${{ number_format($debt->paid_amount, 2) }}</span>
                                    <span>Total: ${{ number_format($debt->total_amount, 2) }}</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                    <div class="h-2.5 bg-indigo-500 rounded-full" style="width: {{ $debt->progress_percent }}%"></div>
                                </div>
                                <div class="text-xs text-gray-400 mt-2">Pendiente: ${{ number_format($debt->outstanding_balance, 2) }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
