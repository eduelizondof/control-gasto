<x-guest-layout>
    {{-- Title --}}
    <div class="text-center mb-6">
        <div
            class="w-14 h-14 rounded-2xl bg-cyan-500/15 border border-cyan-500/20 flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                </path>
            </svg>
        </div>
        <h2 class="text-xl sm:text-2xl font-bold text-white">Verificar Email</h2>
        <p class="text-indigo-300/60 text-sm mt-2 leading-relaxed max-w-sm mx-auto">
            ¡Gracias por registrarte! Revisa tu correo y haz clic en el enlace de verificación que te enviamos.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div
            class="mb-4 p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm text-center">
            Se ha enviado un nuevo enlace de verificación a tu correo electrónico.
        </div>
    @endif

    <div class="flex flex-col gap-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2 auth-primary-btn">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                    </path>
                </svg>
                Reenviar correo de verificación
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full text-center text-indigo-400 hover:text-indigo-300 text-sm font-medium transition py-2">
                Cerrar sesión
            </button>
        </form>
    </div>
</x-guest-layout>