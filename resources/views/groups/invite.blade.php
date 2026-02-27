<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Invitar al Grupo</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $group->name }}</p>
            </div>
            <a href="{{ route('groups.index') }}" class="text-indigo-600 text-sm font-medium hover:underline">← Mis
                Grupos</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Generic processed message --}}
            @if(session('invite_processed'))
                <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-xl flex items-start gap-3"
                    x-data="{ show: true }" x-show="show" x-transition>
                    <svg class="w-5 h-5 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="flex-1">
                        <span class="text-sm font-bold block mb-1">Petición procesada</span>
                        <span class="text-sm">Si el correo ingresado corresponde a un usuario registrado, se le ha enviado
                            la invitación. De lo contrario, invítalo a ser parte de la comunidad compartiendo este
                            enlace:</span>
                        <div class="mt-3 flex items-center gap-2 max-w-md">
                            <input type="text" value="{{ route('register') }}" readonly id="register-link"
                                class="flex-1 text-xs bg-white border border-emerald-200 rounded-lg px-3 py-2 text-gray-600 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                            <button
                                onclick="navigator.clipboard.writeText(document.getElementById('register-link').value); this.textContent='¡Copiado!'; setTimeout(() => this.textContent='Copiar', 2000)"
                                class="bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-2 rounded-lg text-xs font-semibold transition shrink-0">
                                Copiar
                            </button>
                        </div>
                    </div>
                    <button @click="show = false" class="ml-auto shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif

            {{-- Invite form --}}
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800">Invitar miembro</h3>
                        <p class="text-gray-400 text-sm">Ingresa el correo electrónico de la persona que deseas invitar
                        </p>
                    </div>
                </div>

                <form method="POST" action="{{ route('groups.invite.store', $group) }}">
                    @csrf
                    <div class="mb-6">
                        <x-input-label for="email" value="Correo electrónico" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                            :value="old('email')" required autofocus placeholder="ejemplo@correo.com" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('groups.index') }}"
                            class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium text-sm">Cancelar</a>
                        <x-primary-button>
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Enviar Invitación
                        </x-primary-button>
                    </div>
                </form>
            </div>

            {{-- Pending invitations for this group --}}
            @php
                $pending = $group->pendingUsers()->get();
            @endphp
            @if($pending->isNotEmpty())
                <div class="mt-6 bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4">Invitaciones pendientes</h3>
                    <div class="space-y-3">
                        @foreach($pending as $user)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-indigo-100 rounded-full flex items-center justify-center">
                                        <span class="text-indigo-600 font-bold text-sm">{{ substr($user->name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800 text-sm">{{ $user->name }}</p>
                                        <p class="text-gray-400 text-xs">{{ $user->email }}</p>
                                    </div>
                                </div>
                                <span
                                    class="text-xs font-medium text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full">Pendiente</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>