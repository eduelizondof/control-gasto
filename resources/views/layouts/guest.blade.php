<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ConectaTusFinanzas') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .auth-gradient-bg {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 40%, #312e81 70%, #4338ca 100%);
            min-height: 100vh;
        }

        .auth-card {
            background: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .auth-glow {
            box-shadow: 0 0 80px rgba(99, 102, 241, 0.15), 0 25px 60px rgba(0, 0, 0, 0.3);
        }

        .auth-particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(139, 92, 246, 0.4);
            border-radius: 50%;
            animation: auth-float 15s linear infinite;
        }

        @keyframes auth-float {
            0% {
                transform: translateY(100vh) rotate(0deg);
                opacity: 0;
            }

            10% {
                opacity: 1;
            }

            90% {
                opacity: 1;
            }

            100% {
                transform: translateY(-10vh) rotate(720deg);
                opacity: 0;
            }
        }

        /* Override Breeze input styles for dark theme */
        .auth-card input[type="text"],
        .auth-card input[type="email"],
        .auth-card input[type="password"] {
            background: rgba(255, 255, 255, 0.08) !important;
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
            color: #fff !important;
            border-radius: 0.75rem !important;
            padding: 0.75rem 1rem !important;
            transition: all 0.2s ease;
        }

        .auth-card input[type="text"]:focus,
        .auth-card input[type="email"]:focus,
        .auth-card input[type="password"]:focus {
            border-color: rgba(129, 140, 248, 0.5) !important;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2) !important;
            background: rgba(255, 255, 255, 0.12) !important;
            outline: none !important;
        }

        .auth-card input::placeholder {
            color: rgba(255, 255, 255, 0.3) !important;
        }

        .auth-card input[type="checkbox"] {
            background: rgba(255, 255, 255, 0.1) !important;
            border-color: rgba(255, 255, 255, 0.2) !important;
        }

        .auth-card input[type="checkbox"]:checked {
            background-color: #6366f1 !important;
            border-color: #6366f1 !important;
        }

        .auth-card label,
        .auth-card .text-gray-600,
        .auth-card .text-gray-700 {
            color: rgba(199, 210, 254, 0.8) !important;
        }

        .auth-card .text-red-600,
        .auth-card .text-red-500 {
            color: #fb7185 !important;
        }

        .auth-card .text-green-600 {
            color: #34d399 !important;
        }

        /* Primary button override */
        .auth-card button[type="submit"],
        .auth-card .auth-primary-btn {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%) !important;
            color: #fff !important;
            border: none !important;
            border-radius: 0.75rem !important;
            padding: 0.75rem 1.5rem !important;
            font-weight: 600 !important;
            font-size: 0.875rem !important;
            letter-spacing: 0.025em !important;
            transition: all 0.3s ease !important;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3) !important;
        }

        .auth-card button[type="submit"]:hover,
        .auth-card .auth-primary-btn:hover {
            background: linear-gradient(135deg, #818cf8 0%, #a78bfa 100%) !important;
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4) !important;
            transform: translateY(-1px) !important;
        }
    </style>
</head>

<body class="antialiased">
    <div class="auth-gradient-bg relative overflow-hidden flex flex-col items-center justify-center px-4 py-8 sm:py-12">
        {{-- Particles --}}
        <div class="auth-particle" style="left: 10%; animation-delay: 0s;"></div>
        <div class="auth-particle" style="left: 30%; animation-delay: 4s;"></div>
        <div class="auth-particle" style="left: 60%; animation-delay: 7s;"></div>
        <div class="auth-particle" style="left: 80%; animation-delay: 11s;"></div>
        <div class="auth-particle" style="left: 45%; animation-delay: 2s; width: 6px; height: 6px;"></div>

        {{-- Decorative gradient orbs --}}
        <div
            class="absolute top-0 right-0 w-72 sm:w-96 h-72 sm:h-96 bg-purple-600/20 rounded-full blur-3xl pointer-events-none">
        </div>
        <div
            class="absolute bottom-0 left-0 w-72 sm:w-96 h-72 sm:h-96 bg-indigo-600/20 rounded-full blur-3xl pointer-events-none">
        </div>

        {{-- Logo --}}
        <div class="relative z-10 mb-6 sm:mb-8">
            <a href="/" class="flex flex-col items-center gap-3 group">
                <div
                    class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-white/10 border border-white/15 flex items-center justify-center shadow-lg shadow-indigo-500/20 group-hover:shadow-indigo-500/30 transition-all group-hover:scale-105">
                    <img src="https://conectivaits.com/images/logo/logo-icono-blanco.png" alt="ConectaTusFinanzas"
                        class="w-10 h-10 sm:w-12 sm:h-12 object-contain">
                </div>
                <span class="text-white font-bold text-xl sm:text-2xl tracking-tight">ConectaTusFinanzas</span>
            </a>
        </div>

        {{-- Card --}}
        <div class="auth-card auth-glow w-full max-w-md rounded-2xl sm:rounded-3xl p-6 sm:p-8 relative z-10">
            {{ $slot }}
        </div>

        {{-- Back to home --}}
        <div class="relative z-10 mt-6">
            <a href="/" class="text-indigo-300/60 hover:text-indigo-300 text-sm transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver al inicio
            </a>
        </div>
    </div>
</body>

</html>