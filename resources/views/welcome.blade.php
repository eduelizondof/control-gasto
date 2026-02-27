<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description"
        content="ConectaTusFinanzas - Sistema inteligente de gestión de gastos personales y familiares">
    <title>ConectaTusFinanzas — Control de Gastos Familiar</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 40%, #312e81 70%, #4338ca 100%);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.12);
        }

        .glow {
            box-shadow: 0 0 60px rgba(99, 102, 241, 0.3), 0 0 120px rgba(139, 92, 246, 0.1);
        }

        .float-animation {
            animation: float 6s ease-in-out infinite;
        }

        .float-animation-delay {
            animation: float 6s ease-in-out 2s infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        @keyframes gradient-shift {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .gradient-text {
            background: linear-gradient(135deg, #818cf8 0%, #c084fc 50%, #f472b6 100%);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: gradient-shift 4s ease infinite;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .feature-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(139, 92, 246, 0.4);
            border-radius: 50%;
            animation: particle-float 15s linear infinite;
        }

        @keyframes particle-float {
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

        /* Mobile menu */
        .mobile-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out, opacity 0.3s ease-out;
            opacity: 0;
        }

        .mobile-menu.open {
            max-height: 200px;
            opacity: 1;
        }
    </style>
</head>

<body class="antialiased">
    <!-- Navigation -->
    <nav class="fixed top-0 w-full z-50 bg-slate-900/80 backdrop-blur-xl border-b border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="/" class="flex items-center gap-3">
                    <img src="https://conectivaits.com/images/logo/logo-icono-blanco.png" alt="ConectaTusFinanzas"
                        class="w-9 h-9 rounded-xl shadow-lg shadow-indigo-500/30 object-contain">
                    <span class="text-white font-bold text-lg sm:text-xl tracking-tight">ConectaTusFinanzas</span>
                </a>

                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}"
                            class="w-10 h-10 rounded-full bg-indigo-600/30 border border-indigo-500/40 flex items-center justify-center text-indigo-300 hover:bg-indigo-600/50 hover:text-white transition-all"
                            title="Mi Panel">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </a>
                    @else
                        <!-- Desktop buttons -->
                        <div class="hidden sm:flex items-center gap-3">
                            <a href="{{ route('login') }}"
                                class="text-gray-300 hover:text-white transition font-medium text-sm">
                                Iniciar Sesión
                            </a>
                            <a href="{{ route('register') }}"
                                class="bg-indigo-600 hover:bg-indigo-500 text-white px-5 py-2 rounded-xl font-semibold text-sm transition shadow-lg shadow-indigo-600/30 hover:shadow-indigo-500/40">
                                Crear Cuenta
                            </a>
                        </div>
                        <!-- Mobile hamburger -->
                        <button id="mobile-menu-btn"
                            class="sm:hidden w-10 h-10 rounded-xl bg-white/10 border border-white/10 flex items-center justify-center text-gray-300 hover:text-white hover:bg-white/20 transition-all"
                            aria-label="Menú">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                    @endauth
                </div>
            </div>

            <!-- Mobile menu dropdown -->
            @guest
                <div id="mobile-menu" class="mobile-menu sm:hidden border-t border-white/10">
                    <div class="py-3 space-y-2">
                        <a href="{{ route('login') }}"
                            class="block w-full text-center text-gray-300 hover:text-white transition font-medium text-sm py-2.5 rounded-xl hover:bg-white/10">
                            Iniciar Sesión
                        </a>
                        <a href="{{ route('register') }}"
                            class="block w-full text-center bg-indigo-600 hover:bg-indigo-500 text-white py-2.5 rounded-xl font-semibold text-sm transition shadow-lg shadow-indigo-600/30">
                            Crear Cuenta
                        </a>
                    </div>
                </div>
            @endguest
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="gradient-bg relative overflow-hidden min-h-screen flex items-center">
        <!-- Particles -->
        <div class="particle" style="left: 10%; animation-delay: 0s;"></div>
        <div class="particle" style="left: 25%; animation-delay: 3s;"></div>
        <div class="particle" style="left: 50%; animation-delay: 6s;"></div>
        <div class="particle" style="left: 70%; animation-delay: 9s;"></div>
        <div class="particle" style="left: 85%; animation-delay: 12s;"></div>
        <div class="particle" style="left: 40%; animation-delay: 1s; width: 6px; height: 6px;"></div>
        <div class="particle" style="left: 60%; animation-delay: 4s; width: 3px; height: 3px;"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 sm:pt-32 pb-16 sm:pb-20 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                <!-- Left -->
                <div class="text-center lg:text-left">
                    <div
                        class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-full px-4 py-1.5 text-sm text-indigo-200 mb-6 sm:mb-8">
                        <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                        Gestión financiera inteligente
                    </div>

                    <h1 class="text-4xl sm:text-5xl lg:text-7xl font-black text-white leading-tight mb-6">
                        Controla tus
                        <span class="gradient-text">finanzas</span>
                        en familia
                    </h1>

                    <p
                        class="text-lg sm:text-xl text-indigo-200/80 mb-8 sm:mb-10 leading-relaxed max-w-xl mx-auto lg:mx-0">
                        Organiza ingresos, gastos, presupuestos y deudas de tu hogar en un solo lugar. Visualiza tu
                        panorama financiero y toma mejores decisiones.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center lg:justify-start">
                        <a href="{{ route('register') }}"
                            class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white px-8 py-4 rounded-2xl font-bold text-base sm:text-lg transition-all duration-300 shadow-xl shadow-indigo-600/30 hover:shadow-indigo-500/40 hover:-translate-y-0.5 text-center">
                            Comenzar Gratis
                        </a>
                        <a href="#features"
                            class="glass-card text-white px-8 py-4 rounded-2xl font-semibold text-base sm:text-lg transition-all hover:bg-white/15 text-center">
                            Ver Funciones →
                        </a>
                    </div>

                    <!-- Stats -->
                    <div class="flex gap-8 sm:gap-10 mt-10 sm:mt-14 justify-center lg:justify-start">
                        <div>
                            <div class="text-2xl sm:text-3xl font-black text-white">100%</div>
                            <div class="text-indigo-300/70 text-xs sm:text-sm mt-1">Privado y seguro</div>
                        </div>
                        <div>
                            <div class="text-2xl sm:text-3xl font-black text-white">∞</div>
                            <div class="text-indigo-300/70 text-xs sm:text-sm mt-1">Movimientos</div>
                        </div>
                        <div>
                            <div class="text-2xl sm:text-3xl font-black text-white">Fácil</div>
                            <div class="text-indigo-300/70 text-xs sm:text-sm mt-1">De usar</div>
                        </div>
                    </div>
                </div>

                <!-- Right - Dashboard Preview -->
                <div class="relative float-animation hidden lg:block">
                    <div class="glass-card rounded-3xl p-6 glow">
                        <div class="bg-slate-800/80 rounded-2xl p-5 mb-4">
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-gray-400 text-sm font-medium">Resumen del Mes</span>
                                <span class="text-indigo-400 text-sm">Febrero 2025</span>
                            </div>
                            <div class="text-4xl font-black text-white mb-1">$32,450.00</div>
                            <div class="text-emerald-400 text-sm font-semibold">Balance total</div>
                        </div>

                        <div class="grid grid-cols-3 gap-3 mb-4">
                            <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-xl p-3 text-center">
                                <div class="text-emerald-400 font-bold text-lg">$30,000</div>
                                <div class="text-emerald-400/60 text-xs mt-0.5">Ingresos</div>
                            </div>
                            <div class="bg-rose-500/10 border border-rose-500/20 rounded-xl p-3 text-center">
                                <div class="text-rose-400 font-bold text-lg">$11,528</div>
                                <div class="text-rose-400/60 text-xs mt-0.5">Gastos</div>
                            </div>
                            <div class="bg-indigo-500/10 border border-indigo-500/20 rounded-xl p-3 text-center">
                                <div class="text-indigo-400 font-bold text-lg">$3,000</div>
                                <div class="text-indigo-400/60 text-xs mt-0.5">Ahorro</div>
                            </div>
                        </div>

                        <div class="bg-slate-800/80 rounded-2xl p-4">
                            <div class="text-gray-400 text-xs font-medium mb-3">Últimos movimientos</div>
                            <div class="space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-rose-500/20 rounded-lg flex items-center justify-center">
                                            <span class="text-xs">🏠</span>
                                        </div>
                                        <div>
                                            <div class="text-white text-sm font-medium">Renta</div>
                                            <div class="text-gray-500 text-xs">Vivienda</div>
                                        </div>
                                    </div>
                                    <span class="text-rose-400 font-semibold text-sm">-$8,000</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 bg-emerald-500/20 rounded-lg flex items-center justify-center">
                                            <span class="text-xs">💰</span>
                                        </div>
                                        <div>
                                            <div class="text-white text-sm font-medium">Quincena</div>
                                            <div class="text-gray-500 text-xs">Salario</div>
                                        </div>
                                    </div>
                                    <span class="text-emerald-400 font-semibold text-sm">+$15,000</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 bg-amber-500/20 rounded-lg flex items-center justify-center">
                                            <span class="text-xs">🛒</span>
                                        </div>
                                        <div>
                                            <div class="text-white text-sm font-medium">Supermercado</div>
                                            <div class="text-gray-500 text-xs">Alimentación</div>
                                        </div>
                                    </div>
                                    <span class="text-rose-400 font-semibold text-sm">-$1,200</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Decorative gradient orbs -->
        <div class="absolute top-1/4 right-0 w-96 h-96 bg-purple-600/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-1/4 w-96 h-96 bg-indigo-600/20 rounded-full blur-3xl"></div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-16 sm:py-24 bg-white relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 sm:mb-16">
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-gray-900 mb-4">Todo lo que necesitas</h2>
                <p class="text-lg sm:text-xl text-gray-500 max-w-2xl mx-auto">Herramientas poderosas para el control
                    total de las
                    finanzas de tu familia.</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                <!-- Feature 1 -->
                <div
                    class="feature-card bg-gradient-to-br from-slate-50 to-indigo-50/50 rounded-2xl p-6 sm:p-8 border border-gray-100">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-indigo-500/25">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Múltiples Cuentas</h3>
                    <p class="text-gray-500 leading-relaxed">Gestiona efectivo, cuentas de débito, crédito, inversiones
                        y fondos de ahorro desde un solo lugar.</p>
                </div>

                <!-- Feature 2 -->
                <div
                    class="feature-card bg-gradient-to-br from-slate-50 to-emerald-50/50 rounded-2xl p-6 sm:p-8 border border-gray-100">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-emerald-500/25">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Presupuesto Mensual</h3>
                    <p class="text-gray-500 leading-relaxed">Define cuánto necesitas para vivir. Calcula tu gasto base
                        mensual y detecta fugas de dinero automáticamente.</p>
                </div>

                <!-- Feature 3 -->
                <div
                    class="feature-card bg-gradient-to-br from-slate-50 to-rose-50/50 rounded-2xl p-6 sm:p-8 border border-gray-100">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-rose-500 to-pink-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-rose-500/25">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Recordatorios</h3>
                    <p class="text-gray-500 leading-relaxed">Agenda de pagos fijos, cortes de tarjeta, vencimientos y
                        cualquier compromiso financiero recurrente.</p>
                </div>

                <!-- Feature 4 -->
                <div
                    class="feature-card bg-gradient-to-br from-slate-50 to-amber-50/50 rounded-2xl p-6 sm:p-8 border border-gray-100">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-amber-500/25">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Control de Deudas</h3>
                    <p class="text-gray-500 leading-relaxed">Monitorea préstamos, MSI y créditos. Conoce tu capacidad de
                        endeudamiento en todo momento.</p>
                </div>

                <!-- Feature 5 -->
                <div
                    class="feature-card bg-gradient-to-br from-slate-50 to-cyan-50/50 rounded-2xl p-6 sm:p-8 border border-gray-100">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-cyan-500/25">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Grupos Familiares</h3>
                    <p class="text-gray-500 leading-relaxed">Comparte la gestión financiera con tu pareja o familia.
                        Diferentes roles y permisos para cada miembro.</p>
                </div>

                <!-- Feature 6 -->
                <div
                    class="feature-card bg-gradient-to-br from-slate-50 to-violet-50/50 rounded-2xl p-6 sm:p-8 border border-gray-100">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-violet-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-violet-500/25">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Cortes de Periodo</h3>
                    <p class="text-gray-500 leading-relaxed">Snapshots mensuales para comparar tu gasto estimado vs
                        real. Detecta exactamente dónde se fuga tu dinero.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How it works -->
    <section class="py-16 sm:py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 sm:mb-16">
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-gray-900 mb-4">¿Cómo funciona?</h2>
                <p class="text-lg sm:text-xl text-gray-500 max-w-2xl mx-auto">En 3 simples pasos tendrás el control
                    total de tus
                    finanzas.</p>
            </div>

            <div class="grid sm:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-20 h-20 bg-indigo-100 rounded-3xl flex items-center justify-center mx-auto mb-6">
                        <span class="text-4xl font-black text-indigo-600">1</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Crea tu grupo</h3>
                    <p class="text-gray-500 leading-relaxed">Regístrate y crea un grupo para ti o tu familia. Agrega tus
                        cuentas bancarias y efectivo.</p>
                </div>

                <div class="text-center">
                    <div class="w-20 h-20 bg-purple-100 rounded-3xl flex items-center justify-center mx-auto mb-6">
                        <span class="text-4xl font-black text-purple-600">2</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Registra movimientos</h3>
                    <p class="text-gray-500 leading-relaxed">Captura tus ingresos, gastos y transferencias. Organízalos
                        por categorías y conceptos.</p>
                </div>

                <div class="text-center">
                    <div class="w-20 h-20 bg-emerald-100 rounded-3xl flex items-center justify-center mx-auto mb-6">
                        <span class="text-4xl font-black text-emerald-600">3</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Visualiza tu panorama</h3>
                    <p class="text-gray-500 leading-relaxed">Tu dashboard te muestra el estado real de tus finanzas:
                        saldos, presupuesto, deudas y ahorro.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="gradient-bg relative py-16 sm:py-24 overflow-hidden">
        <div class="absolute inset-0">
            <div class="particle" style="left: 15%; animation-delay: 2s;"></div>
            <div class="particle" style="left: 55%; animation-delay: 5s;"></div>
            <div class="particle" style="left: 80%; animation-delay: 8s;"></div>
        </div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white mb-6">
                Toma el control de tu
                <span class="gradient-text">dinero hoy</span>
            </h2>
            <p class="text-lg sm:text-xl text-indigo-200/80 mb-8 sm:mb-10 max-w-2xl mx-auto">
                Deja de preocuparte por tus finanzas. ConectaTusFinanzas te da la claridad que necesitas para alcanzar
                tus metas financieras.
            </p>
            <a href="{{ route('register') }}"
                class="inline-flex items-center gap-2 bg-white text-indigo-700 px-8 sm:px-10 py-4 rounded-2xl font-bold text-base sm:text-lg transition-all duration-300 shadow-xl hover:shadow-2xl hover:-translate-y-1">
                Crear Cuenta Gratis
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6">
                    </path>
                </svg>
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 border-t border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Main footer content -->
            <div class="py-10 sm:py-12">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    <!-- Brand -->
                    <div class="sm:col-span-2 lg:col-span-1">
                        <a href="/" class="flex items-center gap-3 mb-4">
                            <img src="https://conectivaits.com/images/logo/logo-icono-blanco.png"
                                alt="ConectaTusFinanzas" class="w-8 h-8 rounded-lg object-contain">
                            <span class="text-white font-bold text-lg">ConectaTusFinanzas</span>
                        </a>
                        <p class="text-gray-400 text-sm leading-relaxed max-w-xs">
                            Sistema inteligente de gestión de gastos personales y familiares.
                        </p>
                    </div>

                    <!-- Links -->
                    <div>
                        <h4 class="text-white font-semibold text-sm uppercase tracking-wider mb-4">Producto</h4>
                        <ul class="space-y-2.5">
                            <li><a href="#features"
                                    class="text-gray-400 hover:text-indigo-400 text-sm transition">Funciones</a></li>
                            <li><a href="{{ route('register') }}"
                                    class="text-gray-400 hover:text-indigo-400 text-sm transition">Crear Cuenta</a></li>
                            <li><a href="{{ route('login') }}"
                                    class="text-gray-400 hover:text-indigo-400 text-sm transition">Iniciar Sesión</a>
                            </li>
                        </ul>
                    </div>

                    <!-- Legal -->
                    <div>
                        <h4 class="text-white font-semibold text-sm uppercase tracking-wider mb-4">Soporte</h4>
                        <ul class="space-y-2.5">
                            <li><a href="#" class="text-gray-400 hover:text-indigo-400 text-sm transition">Ayuda</a>
                            </li>
                            <li><a href="#" class="text-gray-400 hover:text-indigo-400 text-sm transition">Contacto</a>
                            </li>
                            <li><a href="#"
                                    class="text-gray-400 hover:text-indigo-400 text-sm transition">Privacidad</a></li>
                        </ul>
                    </div>

                    <!-- Powered by -->
                    <div>
                        <h4 class="text-white font-semibold text-sm uppercase tracking-wider mb-4">Desarrollado por</h4>
                        <a href="https://conectivaits.com" target="_blank" rel="noopener"
                            class="inline-flex items-center gap-2 text-indigo-400 hover:text-indigo-300 text-sm transition group">
                            <img src="https://conectivaits.com/images/logo/logo-icono-blanco.png" alt="ConectivaITS"
                                class="w-6 h-6 rounded object-contain opacity-70 group-hover:opacity-100 transition">
                            ConectivaITS
                        </a>
                        <p class="text-gray-500 text-xs mt-2">Soluciones tecnológicas empresariales</p>
                    </div>
                </div>
            </div>

            <!-- Bottom bar -->
            <div class="border-t border-white/10 py-5 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-gray-500 text-xs text-center sm:text-left">
                    © {{ date('Y') }} ConectaTusFinanzas. Todos los derechos reservados.
                </p>
                <div class="flex items-center gap-1 text-gray-600 text-xs">
                    <span>Hecho con</span>
                    <svg class="w-3.5 h-3.5 text-rose-500" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                    </svg>
                    <span>en México</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Mobile menu toggle script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btn = document.getElementById('mobile-menu-btn');
            const menu = document.getElementById('mobile-menu');
            if (btn && menu) {
                btn.addEventListener('click', function () {
                    menu.classList.toggle('open');
                    // Toggle hamburger/close icon
                    const svg = btn.querySelector('svg');
                    if (menu.classList.contains('open')) {
                        svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>';
                    } else {
                        svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>';
                    }
                });
            }
        });
    </script>
</body>

</html>