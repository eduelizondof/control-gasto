@php
    $columns = [
        [
            'title' => 'Cuentas Comunes',
            'groups' => $dailyAccounts,
            'emptyText' => 'No hay cuentas comunes registradas.',
        ],
        [
            'title' => 'Ahorro e Inversión',
            'groups' => $savingsAccounts,
            'emptyText' => 'No hay cuentas de ahorro o inversión registradas.',
        ]
    ];
@endphp

<div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col h-full">
    <div class="flex items-center justify-between mb-4 shrink-0">
        <h3 class="text-lg font-bold text-gray-800">Mis Cuentas</h3>
        <a href="{{ route('accounts.index', $group) }}" class="text-indigo-600 text-sm font-medium hover:underline">Ver todas →</a>
    </div>
    
    <div class="grid lg:grid-cols-2 gap-8 overflow-y-auto max-h-[500px] pr-2 pb-1 custom-scrollbar">
        @foreach($columns as $col)
            <div>
                <h3 class="text-sm font-bold text-gray-800 border-b border-gray-100 pb-2 mb-4">{{ $col['title'] }}</h3>
                
                @forelse($col['groups'] as $categoryName => $categoryAccounts)
                    <div class="mb-5 last:mb-0">
                        <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 pl-1">{{ $categoryName }}</h4>
                        <div class="grid sm:grid-cols-2 gap-3">
                            @foreach($categoryAccounts as $account)
                                <a href="{{ route('accounts.edit', [$group, $account]) }}" class="block rounded-xl p-3 border border-gray-100 hover:border-indigo-300 hover:shadow-sm transition bg-white cursor-pointer group">
                                    <div class="flex items-center gap-3 mb-2">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0" style="background-color: {{ $account->color }}15; color: {{ $account->color ?? '#6366f1' }};">
                                            @switch($account->type)
                                                @case('cash')
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="12" x="2" y="6" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/></svg>
                                                    @break
                                                @case('debit')
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                                                    @break
                                                @case('credit')
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/><path d="M6 15h.01M10 15h2"/></svg>
                                                    @break
                                                @case('savings')
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.5 2.8C2.1 10.9 2 11.2 2 11.5V17c0 .6.4 1 1 1h2"/><path d="M12 17h2"/><circle cx="15" cy="17" r="2"/><circle cx="5" cy="17" r="2"/></svg>
                                                    @break
                                                @case('investment')
                                                @case('fund')
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>
                                                    @break
                                                @case('emergency')
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                                    @break
                                                @default
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"/><path d="M3 5v14a2 2 0 0 0 2 2h16v-5"/><path d="M18 12a2 2 0 0 0 0 4h4v-4Z"/></svg>
                                            @endswitch
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="font-semibold text-gray-800 text-sm truncate group-hover:text-indigo-600 transition-colors">{{ $account->name }}</div>
                                            <div class="text-gray-400 text-[10px] mt-0.5">{{ $account->type_labels }}</div>
                                        </div>
                                    </div>
                                    <div class="text-sm font-black {{ $account->current_balance >= 0 ? 'text-gray-900 group-hover:text-indigo-700 transition-colors' : 'text-rose-600' }}">
                                        ${{ number_format($account->current_balance, 2) }}
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 text-gray-500 text-sm border border-dashed border-gray-200 rounded-xl">
                        {{ $col['emptyText'] }}
                    </div>
                @endforelse
            </div>
        @endforeach
    </div>
</div>
