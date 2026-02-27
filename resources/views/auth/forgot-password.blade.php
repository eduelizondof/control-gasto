<x-guest-layout>
    {{-- Title --}}
    <div class="text-center mb-6">
        <div
            class="w-14 h-14 rounded-2xl bg-amber-500/15 border border-amber-500/20 flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z">
                </path>
            </svg>
        </div>
        <h2 class="text-xl sm:text-2xl font-bold text-white">Recuperar Contraseña</h2>
        <p class="text-indigo-300/60 text-sm mt-2 leading-relaxed max-w-sm mx-auto">
            Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Correo electrónico')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required
                autofocus placeholder="tu@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Submit -->
        <div class="mt-6">
            <button type="submit" class="w-full flex items-center justify-center gap-2 auth-primary-btn">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                    </path>
                </svg>
                Enviar enlace de recuperación
            </button>
        </div>
    </form>

    {{-- Back to login --}}
    <div class="mt-6 pt-5 border-t border-white/10 text-center">
        <a href="{{ route('login') }}"
            class="inline-flex items-center gap-1.5 text-indigo-400 hover:text-indigo-300 font-semibold text-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                </path>
            </svg>
            Volver a iniciar sesión
        </a>
    </div>
</x-guest-layout>