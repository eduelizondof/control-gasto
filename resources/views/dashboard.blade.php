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
                            $budgetDiff = $budgetTotal - $budgetSpent;
                            $budgetPercent = $budgetTotal > 0 ? min(100, round(($budgetSpent / $budgetTotal) * 100)) : 0;
                        @endphp
                        {{-- Global bar --}}
                        <div class="mb-5">
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-600">Gastado: ${{ number_format($budgetSpent, 2) }}</span>
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

                        {{-- Out of budget breakdown --}}
                        @if($outOfBudgetBreakdown->isNotEmpty())
                            <div class="border-t border-rose-100 mt-5 pt-4">
                                <div class="flex justify-between items-center mb-3">
                                    <p class="text-xs font-bold text-rose-500 uppercase tracking-wider">Fuera de Presupuesto</p>
                                    <span class="text-xs font-bold text-rose-600">${{ number_format($outOfBudgetTotal, 2) }}</span>
                                </div>
                                <div class="space-y-3 max-h-48 overflow-y-auto pr-1">
                                    @foreach($outOfBudgetBreakdown as $entry)
                                        <div>
                                            <div class="flex items-center justify-between text-xs mb-1">
                                                <div class="flex items-center gap-1.5 min-w-0">
                                                    <span class="w-2 h-2 rounded-full shrink-0" style="background-color: {{ $entry->category?->color ?? '#e2e8f0' }}"></span>
                                                    <span class="font-medium text-gray-700 truncate">{{ $entry->name }}</span>
                                                </div>
                                                <span class="font-bold shrink-0 ml-2 text-rose-600">
                                                    ${{ number_format($entry->spent, 2) }}
                                                </span>
                                            </div>
                                            <div class="w-full bg-rose-50 rounded-full h-1.5 overflow-hidden">
                                                <div class="h-1.5 bg-rose-400 rounded-full" style="width: 100%"></div>
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

            <!-- Quincena Analysis & Savings Tips -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="bg-indigo-900 rounded-2xl p-6 text-white shadow-lg overflow-hidden relative">
                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <h3 class="text-lg font-bold">Distribución de Pagos</h3>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-white/10 rounded-xl p-3 border border-white/10">
                                <p class="text-indigo-100 text-[10px] font-bold uppercase mb-2">1a Quincena (1-15)</p>
                                <div class="flex justify-between items-end">
                                    <div>
                                        <p class="text-2xl font-black">${{ number_format($q1Load, 0) }}</p>
                                        <p class="text-[10px] text-indigo-300">Total en pagos</p>
                                    </div>
                                    @if($q1Pending > 0)
                                        <span class="text-[10px] bg-amber-500/20 text-amber-300 px-1.5 py-0.5 rounded font-bold">${{ number_format($q1Pending, 0) }} pend.</span>
                                    @else
                                        <span class="text-[10px] bg-emerald-500/20 text-emerald-300 px-1.5 py-0.5 rounded font-bold">Cubierto</span>
                                    @endif
                                </div>
                            </div>
                            <div class="bg-white/10 rounded-xl p-3 border border-white/10">
                                <p class="text-indigo-100 text-[10px] font-bold uppercase mb-2">2a Quincena (16-31)</p>
                                <div class="flex justify-between items-end">
                                    <div>
                                        <p class="text-2xl font-black">${{ number_format($q2Load, 0) }}</p>
                                        <p class="text-[10px] text-indigo-300">Total en pagos</p>
                                    </div>
                                    @if($q2Pending > 0)
                                        <span class="text-[10px] bg-amber-500/20 text-amber-300 px-1.5 py-0.5 rounded font-bold">${{ number_format($q2Pending, 0) }} pend.</span>
                                    @else
                                        <span class="text-[10px] bg-emerald-500/20 text-emerald-300 px-1.5 py-0.5 rounded font-bold">Cubierto</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/5 rounded-full"></div>
                </div>

                <div class="bg-white rounded-2xl p-6 border {{ $q1Load > $q2Load ? 'border-amber-200 bg-amber-50/30' : 'border-indigo-100' }} flex items-center gap-4">
                    <div class="w-12 h-12 {{ $q1Load > $q2Load ? 'bg-amber-100 text-amber-600' : 'bg-indigo-100 text-indigo-600' }} rounded-2xl flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800">Consejo Quincenal</h4>
                        <p class="text-sm text-gray-600 mt-1">
                            @php
                                $isFirstQuincena = now()->day <= 15;
                            @endphp
                            @if($isFirstQuincena)
                                @if($q1Pending > 0)
                                    Te faltan <span class="font-bold text-amber-600">${{ number_format($q1Pending, 0) }}</span> para cubrir esta quincena.
                                @else
                                    ¡Genial! Ya cubriste los pagos de esta quincena. Empieza a guardar para la siguiente (<span class="font-bold text-indigo-600">${{ number_format($q2Pending, 0) }}</span> pendientes).
                                @endif
                            @else
                                @if($q2Pending > 0)
                                    Necesitas <span class="font-bold text-amber-600">${{ number_format($q2Pending, 0) }}</span> para los pagos restantes del mes.
                                @else
                                    Todos los pagos registrados para este mes han sido cubiertos.
                                @endif
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions + Upcoming Reminders -->
            <div class="grid lg:grid-cols-3 gap-6 mb-8">
                <!-- Recent Transactions -->
                <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-100" x-data="{}">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Últimos Movimientos</h3>
                        <a href="{{ route('transactions.index', $group) }}" class="text-indigo-600 text-sm font-medium hover:underline">Ver todos →</a>
                    </div>
                    <div class="space-y-3">
                        @forelse($recentTransactions as $txn)
                            <div @click="$dispatch('open-transaction-modal', { id: {{ $txn->id }}, groupId: {{ $group->id }} })" 
                                class="flex items-center justify-between py-2 px-2 -mx-2 rounded-xl cursor-pointer hover:bg-gray-50 transition {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: {{ $txn->category->color }}15">
                                        <div class="w-3 h-3 rounded-full" style="background-color: {{ $txn->category->color }}"></div>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-medium text-gray-800 text-sm truncate">{{ $txn->description ?: $txn->concept?->name ?: $txn->category->name }}</div>
                                        <div class="text-gray-400 text-xs">{{ $txn->date->translatedFormat('d M') }} · {{ $txn->sourceAccount->name }}</div>
                                    </div>
                                </div>
                                <span class="font-bold text-sm shrink-0 ml-4 {{ in_array($txn->type, ['income']) ? 'text-emerald-600' : 'text-gray-800' }}">
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
                            <div class="flex items-center gap-3 p-3 rounded-xl border {{ $reminder->next_date && $reminder->next_date->isToday() ? 'bg-rose-50 border-rose-100' : 'bg-amber-50/50 border-amber-100/50' }}">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background-color: {{ $reminder->category?->color ?? '#fef3c7' }}{{ $reminder->category?->color ? '20' : '' }}">
                                    @if($reminder->category)
                                        <div class="w-3 h-3 rounded-full" style="background-color: {{ $reminder->category->color }}"></div>
                                    @else
                                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <div class="font-medium text-gray-800 text-sm truncate">{{ $reminder->name }}</div>
                                    <div class="text-gray-400 text-xs">{{ $reminder->next_date?->translatedFormat('d M Y') }}</div>
                                    @if($reminder->estimated_amount)
                                        <div class="text-indigo-600 text-xs font-bold leading-none mt-1">${{ number_format($reminder->estimated_amount, 2) }}</div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-400 text-sm py-4 text-center">Sin pagos próximos.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Income Comparison & Monthly Chart --}}
            @if($configuredIncome > 0 || $avgIncome12m > 0)
            <div class="grid lg:grid-cols-3 gap-6 mb-8">
                {{-- Income Comparison --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        Ingreso: Config vs Real
                    </h3>

                    <div class="space-y-4">
                        @if($configuredFixedIncome > 0)
                        <div class="p-3 bg-gray-50 rounded-xl">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Ingreso Fijo Configurado</p>
                            <p class="text-xl font-black text-gray-800">${{ number_format($configuredFixedIncome, 2) }}</p>
                        </div>
                        @endif

                        @if($configuredIncome > 0)
                        <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-100">
                            <p class="text-xs font-bold text-emerald-600 uppercase tracking-wider mb-1">Ingreso Total Configurado</p>
                            <p class="text-xl font-black text-emerald-700">${{ number_format($configuredIncome, 2) }}</p>
                        </div>
                        @endif

                        <div class="p-3 bg-indigo-50 rounded-xl border border-indigo-100">
                            <p class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-1">Promedio Real (12 meses)</p>
                            <p class="text-xl font-black text-indigo-700">${{ number_format($avgIncome12m, 2) }}</p>
                        </div>

                        @if($configuredIncome > 0 && $avgIncome12m > 0)
                        <div class="p-3 rounded-xl {{ $incomeDiff >= 0 ? 'bg-emerald-50 border border-emerald-100' : 'bg-rose-50 border border-rose-100' }}">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-bold {{ $incomeDiff >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                    {{ $incomeDiff >= 0 ? '▲' : '▼' }} {{ abs($incomeDiffPercent) }}%
                                </p>
                                <p class="text-xs {{ $incomeDiff >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $incomeDiff >= 0 ? '+' : '-' }}${{ number_format(abs($incomeDiff), 2) }}/mes
                                </p>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $incomeDiff >= 0 ? 'Ingresas más de lo configurado' : 'Ingresas menos de lo configurado' }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Monthly Bar Chart --}}
                <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                        Ingresos vs Gastos vs Ahorro
                    </h3>
                    <div class="relative" style="height: 320px;">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>
            </div>
            @endif

            {{-- Expenses By Category --}}
            <div class="grid lg:grid-cols-3 gap-6 mb-8">
                {{-- 12-month Category Pie Chart --}}
                <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                        Gastos por Categoría (últimos 12 meses)
                    </h3>
                    @if(count($chartCategoryData) > 0)
                        <div class="relative flex-1 min-h-[300px]">
                            <canvas id="categoryPieChart"></canvas>
                        </div>
                    @else
                        <div class="flex-1 flex items-center justify-center min-h-[300px] bg-gray-50 rounded-xl border border-dashed border-gray-200">
                            <p class="text-gray-400 text-sm">Sin datos suficientes para mostrar.</p>
                        </div>
                    @endif
                </div>

                {{-- Expenses This Month --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Gastos Este Mes
                    </h3>
                    <div class="space-y-4 overflow-y-auto pr-2 flex-1 max-h-[300px]">
                        @forelse($expensesByCategory as $exp)
                            @php
                                $percent = $monthlyExpenses > 0 ? round(($exp->total / $monthlyExpenses) * 100) : 0;
                            @endphp
                            <div>
                                <div class="flex items-center justify-between text-sm mb-1">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <div class="w-3 h-3 rounded-full shrink-0" style="background-color: {{ $exp->category->color ?? '#9ca3af' }}"></div>
                                        <span class="font-medium text-gray-700 truncate">{{ $exp->category->name ?? 'Sin categoría' }}</span>
                                    </div>
                                    <span class="font-bold text-gray-900 shrink-0 ml-2">${{ number_format($exp->total, 2) }}</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="h-1.5 rounded-full transition-all duration-300" style="width: {{ $percent }}%; background-color: {{ $exp->category->color ?? '#9ca3af' }}"></div>
                                </div>
                                <p class="text-[10px] text-gray-400 mt-1 text-right">{{ $percent }}% del gasto del mes</p>
                            </div>
                        @empty
                            <div class="flex items-center justify-center h-full">
                                <p class="text-gray-400 text-sm text-center">Sin gastos registrados este mes.</p>
                            </div>
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
                                <div class="flex justify-between items-start mb-2">
                                    <div class="font-semibold text-gray-800 text-sm">{{ $debt->name }}</div>
                                    @if($debt->next_payment_date)
                                    <div class="text-right">
                                        <div class="text-xs font-bold text-indigo-600">${{ number_format($debt->payment_amount, 2) }}</div>
                                        <div class="text-[10px] text-gray-500">Próx: {{ $debt->next_payment_date->translatedFormat('d M Y') }}</div>
                                    </div>
                                    @endif
                                </div>
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

    @if(($configuredIncome ?? 0) > 0 || ($avgIncome12m ?? 0) > 0)
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('monthlyChart');
        if (!ctx) return;

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($chartLabels),
                datasets: [
                    {
                        label: 'Ingresos',
                        data: @json($chartIncome),
                        backgroundColor: 'rgba(16, 185, 129, 0.8)',
                        borderColor: 'rgb(16, 185, 129)',
                        borderWidth: 1,
                        borderRadius: 6,
                    },
                    {
                        label: 'Gastos',
                        data: @json($chartExpenses),
                        backgroundColor: 'rgba(244, 63, 94, 0.8)',
                        borderColor: 'rgb(244, 63, 94)',
                        borderWidth: 1,
                        borderRadius: 6,
                    },
                    {
                        label: 'Ahorro',
                        data: @json($chartSavings),
                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 1,
                        borderRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'rectRounded',
                            padding: 20,
                            font: { family: 'Inter', size: 12, weight: '600' }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        titleFont: { family: 'Inter', size: 13 },
                        bodyFont: { family: 'Inter', size: 12 },
                        padding: 12,
                        cornerRadius: 10,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': $' + 
                                    context.parsed.y.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { family: 'Inter', size: 10 },
                            maxRotation: 45,
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: {
                            font: { family: 'Inter', size: 11 },
                            callback: function(value) {
                                return '$' + value.toLocaleString('en-US');
                            }
                        }
                    }
                }
            }
        });

        const pieCtx = document.getElementById('categoryPieChart');
        if (pieCtx && @json(count($chartCategoryData) > 0)) {
            new Chart(pieCtx, {
                type: 'doughnut',
                data: {
                    labels: @json($chartCategoryLabels),
                    datasets: [{
                        data: @json($chartCategoryData),
                        backgroundColor: @json($chartCategoryColors),
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                padding: 15,
                                font: { family: 'Inter', size: 11 }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.9)',
                            titleFont: { family: 'Inter', size: 13 },
                            bodyFont: { family: 'Inter', size: 12 },
                            padding: 12,
                            cornerRadius: 10,
                            callbacks: {
                                label: function(context) {
                                    const value = context.parsed;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                    return ` ${context.label}: $${value.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }
    });
    </script>
    @endpush
    @endif
</x-app-layout>
