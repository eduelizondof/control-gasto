<div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col h-full mb-8" x-data="{}">
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