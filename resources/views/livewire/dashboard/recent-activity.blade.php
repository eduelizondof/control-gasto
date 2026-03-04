<div class="grid lg:grid-cols-3 gap-6 mb-8">
    <!-- Recent Transactions -->
    <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col h-full"
        x-data="{}">
        <div class="flex items-center justify-between mb-4 shrink-0">
            <h3 class="text-lg font-bold text-gray-800">Últimos Movimientos</h3>
            <a href="{{ route('transactions.index', $group) }}"
                class="text-indigo-600 text-sm font-medium hover:underline">Ver todos →</a>
        </div>
        <div class="space-y-3 flex-1 flex flex-col justify-start">
            @forelse($recentTransactions as $txn)
                <div @click="$dispatch('open-transaction-modal', { id: {{ $txn->id }}, groupId: {{ $group->id }} })"
                    class="flex items-center justify-between py-2 px-2 -mx-2 rounded-xl cursor-pointer hover:bg-gray-50 transition {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                            style="background-color: {{ $txn->category->color }}15">
                            <div class="w-3 h-3 rounded-full" style="background-color: {{ $txn->category->color }}"></div>
                        </div>
                        <div class="min-w-0">
                            <div class="font-medium text-gray-800 text-sm truncate">
                                {{ $txn->description ?: $txn->concept?->name ?: $txn->category->name }}</div>
                            <div class="text-gray-400 text-xs">{{ $txn->date->translatedFormat('d M') }} ·
                                {{ $txn->sourceAccount->name }}</div>
                        </div>
                    </div>
                    <span
                        class="font-bold text-sm shrink-0 ml-4 {{ in_array($txn->type, ['income']) ? 'text-emerald-600' : 'text-gray-800' }}">
                        {{ in_array($txn->type, ['income']) ? '+' : '-' }}${{ number_format($txn->amount, 2) }}
                    </span>
                </div>
            @empty
                <p class="text-gray-400 text-sm py-4 text-center">No hay movimientos este mes.</p>
            @endforelse
        </div>
    </div>

    <!-- Upcoming Reminders -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col h-full">
        <div class="flex items-center justify-between mb-4 shrink-0">
            <h3 class="text-lg font-bold text-gray-800">Próximos Pagos</h3>
            <a href="{{ route('reminders.index', $group) }}"
                class="text-indigo-600 text-sm font-medium hover:underline">Ver todos →</a>
        </div>
        <div class="space-y-4 flex-1 flex flex-col justify-start">
            @forelse($upcomingReminders as $reminder)
                <div
                    class="flex items-center gap-3 p-3 rounded-xl border {{ $reminder->next_date && $reminder->next_date->isToday() ? 'bg-rose-50 border-rose-100' : 'bg-amber-50/50 border-amber-100/50' }}">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                        style="background-color: {{ $reminder->category?->color ?? '#fef3c7' }}{{ $reminder->category?->color ? '20' : '' }}">
                        @if($reminder->category)
                            <div class="w-3 h-3 rounded-full" style="background-color: {{ $reminder->category->color }}"></div>
                        @else
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <div class="font-medium text-gray-800 text-sm truncate">{{ $reminder->name }}</div>
                        <div class="text-gray-400 text-xs">{{ $reminder->next_date?->translatedFormat('d M Y') }}</div>
                        @if($reminder->estimated_amount)
                            <div class="text-indigo-600 text-xs font-bold leading-none mt-1">
                                ${{ number_format($reminder->estimated_amount, 2) }}</div>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-gray-400 text-sm py-4 text-center">Sin pagos próximos.</p>
            @endforelse
        </div>
    </div>
</div>