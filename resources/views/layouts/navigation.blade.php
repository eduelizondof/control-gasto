@php
    $group = auth()->user()?->groups()?->first();
    $pendingInvitationsCount = auth()->user()?->pendingInvitations()->count() ?? 0;
@endphp

{{-- ===== Floating Full-Screen Menu Overlay ===== --}}
<div x-data="{ menuOpen: false, touchStartY: 0 }" x-cloak>

    {{-- Overlay backdrop + panel --}}
    <div x-show="menuOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50"
        @click.self="menuOpen = false">

        {{-- Dark backdrop (click to close) --}}
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="menuOpen = false"></div>

        {{-- Menu panel sliding from bottom --}}
        <div x-show="menuOpen" x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
            x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-y-0"
            x-transition:leave-end="translate-y-full"
            class="absolute bottom-[4.5rem] left-3 right-3 bg-white rounded-2xl shadow-2xl overflow-hidden max-h-[75vh] flex flex-col"
            x-on:touchstart.passive="touchStartY = $event.touches[0].clientY"
            x-on:touchend.passive="if ($event.changedTouches[0].clientY - touchStartY > 80) menuOpen = false">

            {{-- Drag handle --}}
            <div class="flex justify-center pt-3 pb-1 cursor-grab">
                <div class="w-10 h-1 rounded-full bg-gray-300"></div>
            </div>

            {{-- User Info Header --}}
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-5 py-4 mx-3 rounded-xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center shrink-0">
                            <span class="text-white font-bold text-base">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-white font-semibold text-sm truncate">{{ Auth::user()->name }}</p>
                            <p class="text-indigo-200 text-xs truncate">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                    {{-- Close button --}}
                    <button @click="menuOpen = false"
                        class="w-8 h-8 flex items-center justify-center rounded-full bg-white/20 hover:bg-white/30 transition shrink-0 ml-2">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Menu Links --}}
            <div class="overflow-y-auto flex-1 py-2">
                {{-- Main navigation --}}
                <div class="px-2">
                    <p class="px-3 pt-3 pb-1 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Navegación
                    </p>

                    <a href="{{ route('dashboard') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition
                              {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1" />
                        </svg>
                        Panel
                    </a>

                    @if($group)
                        <a href="{{ route('accounts.index', $group) }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition
                                                      {{ request()->routeIs('accounts.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            Cuentas
                        </a>

                        <a href="{{ route('transactions.index', $group) }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition
                                                      {{ request()->routeIs('transactions.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                            </svg>
                            Movimientos
                        </a>

                        <a href="{{ route('budgets.index', $group) }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition
                                                      {{ request()->routeIs('budgets.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            Presupuesto
                        </a>

                        <a href="{{ route('categories.index', $group) }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition
                                                      {{ request()->routeIs('categories.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            Categorías
                        </a>

                        <a href="{{ route('concepts.index', $group) }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition
                                                      {{ request()->routeIs('concepts.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            Conceptos
                        </a>

                        <a href="{{ route('debts.index', $group) }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition
                                                      {{ request()->routeIs('debts.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Deudas
                        </a>

                        <a href="{{ route('reminders.index', $group) }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition
                                                      {{ request()->routeIs('reminders.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            Recordatorios
                        </a>
                    @endif
                </div>

                {{-- Divider --}}
                <div class="mx-5 my-2 border-t border-gray-100"></div>

                {{-- Account section --}}
                <div class="px-2">
                    <p class="px-3 pt-1 pb-1 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Cuenta</p>

                    <a href="{{ route('profile.edit') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Mi Perfil
                    </a>

                    <a href="{{ route('groups.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Mis Grupos
                    </a>

                    <a href="{{ route('invitations.index') }}"
                        class="flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition
                              {{ request()->routeIs('invitations.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Invitaciones
                        </div>
                        @if($pendingInvitationsCount > 0)
                            <span class="bg-rose-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">
                                {{ $pendingInvitationsCount }}
                            </span>
                        @endif
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-rose-600 hover:bg-rose-50 transition w-full text-left">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Fixed Bottom Navigation Bar ===== --}}
    <nav
        class="fixed bottom-0 left-0 right-0 z-40 bg-white border-t border-gray-200 shadow-[0_-2px_10px_rgba(0,0,0,0.06)]">
        <div class="flex items-center justify-around h-[4.5rem] max-w-lg mx-auto px-2">

            {{-- 1. Inicio --}}
            <a href="{{ route('dashboard') }}"
                class="flex flex-col items-center justify-center gap-0.5 px-2 py-1 rounded-xl transition
                              {{ request()->routeIs('dashboard') ? 'text-indigo-600' : 'text-gray-400 hover:text-gray-600' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1" />
                </svg>
                <span class="text-[10px] font-semibold leading-tight">Inicio</span>
            </a>

            {{-- 2. Ver Movimientos --}}
            @if($group)
                <a href="{{ route('transactions.index', $group) }}"
                    class="flex flex-col items-center justify-center gap-0.5 px-2 py-1 rounded-xl transition
                                              {{ request()->routeIs('transactions.index') ? 'text-indigo-600' : 'text-gray-400 hover:text-gray-600' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                    </svg>
                    <span class="text-[10px] font-semibold leading-tight">Movimientos</span>
                </a>
            @else
                <div class="flex flex-col items-center justify-center gap-0.5 px-2 py-1 text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                    </svg>
                    <span class="text-[10px] font-semibold leading-tight">Movimientos</span>
                </div>
            @endif

            {{-- 3. Nuevo Movimiento (centro, prominente) --}}
            @if($group)
                <a href="{{ route('transactions.create', $group) }}"
                    class="flex flex-col items-center justify-center -mt-5">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-500/30 ring-4 ring-white">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <span class="text-[10px] font-bold text-indigo-600 mt-0.5 leading-tight">Nuevo</span>
                </a>
            @else
                <div class="flex flex-col items-center justify-center -mt-5 opacity-40">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-gray-400 to-gray-500 rounded-2xl flex items-center justify-center shadow-lg ring-4 ring-white">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <span class="text-[10px] font-bold text-gray-400 mt-0.5 leading-tight">Nuevo</span>
                </div>
            @endif

            {{-- 4. Deudas --}}
            @if($group)
                <a href="{{ route('debts.index', $group) }}"
                    class="flex flex-col items-center justify-center gap-0.5 px-2 py-1 rounded-xl transition
                                              {{ request()->routeIs('debts.*') ? 'text-indigo-600' : 'text-gray-400 hover:text-gray-600' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span class="text-[10px] font-semibold leading-tight">Deudas</span>
                </a>
            @else
                <div class="flex flex-col items-center justify-center gap-0.5 px-2 py-1 text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span class="text-[10px] font-semibold leading-tight">Deudas</span>
                </div>
            @endif

            {{-- 5. Menú --}}
            <button @click="menuOpen = !menuOpen"
                class="flex flex-col items-center justify-center gap-0.5 px-2 py-1 rounded-xl transition"
                :class="menuOpen ? 'text-indigo-600' : 'text-gray-400 hover:text-gray-600'">
                <svg x-show="!menuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg x-show="menuOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span class="text-[10px] font-semibold leading-tight" x-text="menuOpen ? 'Cerrar' : 'Menú'"></span>
            </button>
        </div>

        {{-- Safe area for devices with home indicator --}}
        <div class="h-safe-area-bottom bg-white"></div>
    </nav>
</div>