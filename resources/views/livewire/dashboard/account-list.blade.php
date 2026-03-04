<div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col h-full">
    <div class="flex items-center justify-between mb-4 shrink-0">
        <h3 class="text-lg font-bold text-gray-800">Mis Cuentas</h3>
        <a href="{{ route('accounts.index', $group) }}" class="text-indigo-600 text-sm font-medium hover:underline">Ver todas →</a>
    </div>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 overflow-y-auto max-h-[400px] pr-2 pb-1 custom-scrollbar">
        @foreach($accounts as $account)
            <a href="{{ route('accounts.edit', [$group, $account]) }}" class="block rounded-xl p-4 border border-gray-100 hover:border-indigo-300 hover:shadow-md transition bg-white cursor-pointer group">
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
                <div class="text-xl font-black mt-2 {{ $account->current_balance >= 0 ? 'text-gray-900 group-hover:text-indigo-700 transition-colors' : 'text-rose-600' }}">
                    ${{ number_format($account->current_balance, 2) }}
                </div>
            </a>
        @endforeach
    </div>
</div>
