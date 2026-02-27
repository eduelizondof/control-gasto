<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800">Mis Invitaciones</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            @if($invitations->isEmpty())
                <div class="text-center py-16">
                    <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <p class="text-gray-500 mb-2">No tienes invitaciones pendientes</p>
                    <a href="{{ route('dashboard') }}" class="text-indigo-600 font-medium hover:underline text-sm">← Volver
                        al panel</a>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($invitations as $group)
                        <div
                            class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:border-indigo-200 transition">
                            <div class="flex items-start gap-4">
                                {{-- Group icon --}}
                                <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center shrink-0">
                                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>

                                {{-- Group info --}}
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-bold text-gray-800 text-lg">{{ $group->name }}</h3>
                                    @if($group->description)
                                        <p class="text-gray-500 text-sm mt-1">{{ $group->description }}</p>
                                    @endif
                                    <div class="flex items-center gap-2 mt-2">
                                        <span class="text-xs text-gray-400">
                                            Creado por {{ $group->creator->name ?? 'Usuario' }}
                                        </span>
                                        @if($group->pivot->invited_by)
                                            <span class="text-gray-300">·</span>
                                            <span class="text-xs text-gray-400">
                                                Invitado por
                                                {{ \App\Models\User::find($group->pivot->invited_by)?->name ?? 'Usuario' }}
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Action buttons --}}
                                    <div class="flex items-center gap-3 mt-4">
                                        <form method="POST" action="{{ route('invitations.accept', $group) }}">
                                            @csrf
                                            <button type="submit"
                                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-xl text-sm font-semibold transition shadow-sm flex items-center gap-1.5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7" />
                                                </svg>
                                                Aceptar
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('invitations.reject', $group) }}"
                                            onsubmit="return confirm('¿Seguro que deseas rechazar esta invitación?')">
                                            @csrf
                                            <button type="submit"
                                                class="bg-white hover:bg-rose-50 text-rose-600 border border-rose-200 px-5 py-2 rounded-xl text-sm font-semibold transition flex items-center gap-1.5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                                Rechazar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>