<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800">Cuentas</h2>
            <a href="{{ route('accounts.create', $group) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition shadow-sm">
                + Nueva Cuenta
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($accounts as $account)
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:border-indigo-200 transition {{ !$account->is_active ? 'opacity-60' : '' }}">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
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
                                <div>
                                    <h3 class="font-bold text-gray-800">{{ $account->name }}</h3>
                                    <p class="text-gray-400 text-xs">{{ $account->type_labels }} {{ $account->bank ? '· ' . $account->bank : '' }}</p>
                                </div>
                            </div>
                            @if(!$account->is_active)
                                <span class="text-xs bg-gray-100 text-gray-500 px-2 py-1 rounded-lg">Inactiva</span>
                            @endif
                        </div>

                        <div class="text-3xl font-black mb-2 {{ $account->current_balance >= 0 ? 'text-gray-900' : 'text-rose-600' }}">
                            ${{ number_format($account->current_balance, 2) }}
                        </div>

                        @if($account->type === 'credit' && $account->credit_limit)
                            <div class="mt-3">
                                <div class="flex justify-between text-xs text-gray-500 mb-1">
                                    <span>Usado</span>
                                    <span>Límite: ${{ number_format($account->credit_limit, 2) }}</span>
                                </div>
                                @php $usedPercent = $account->credit_limit > 0 ? min(100, round((abs($account->current_balance) / $account->credit_limit) * 100)) : 0; @endphp
                                <div class="w-full bg-gray-100 rounded-full h-2">
                                    <div class="h-2 bg-purple-500 rounded-full" style="width: {{ $usedPercent }}%"></div>
                                </div>
                                <p class="text-xs text-gray-400 mt-1">Disponible: ${{ number_format($account->available_credit ?? $account->credit_limit, 2) }}</p>
                            </div>
                        @endif

                        <div class="flex items-center gap-2 mt-4 pt-4 border-t border-gray-50">
                            <a href="{{ route('accounts.edit', [$group, $account]) }}" class="text-indigo-600 text-sm font-medium hover:underline">Editar</a>
                            <span class="text-gray-300">·</span>
                            <form method="POST" action="{{ route('accounts.destroy', [$group, $account]) }}" onsubmit="return confirm('¿Eliminar esta cuenta?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-rose-500 text-sm font-medium hover:underline">Eliminar</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <p class="text-gray-500 mb-2">No hay cuentas registradas</p>
                        <a href="{{ route('accounts.create', $group) }}" class="text-indigo-600 font-medium hover:underline">Crear primera cuenta →</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
