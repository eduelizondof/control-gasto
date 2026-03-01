<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ConectaTusFinanzas') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
    @include('layouts.seo')
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-50 pb-24">

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="max-w-7xl mx-auto mt-4 px-4 sm:px-6 lg:px-8">
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg relative"
                    role="alert" x-data="{ show: true }" x-show="show" x-transition>
                    <span class="block sm:inline">{{ session('success') }}</span>
                    <button @click="show = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                        <svg class="fill-current h-5 w-5 text-emerald-500" role="button" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20">
                            <path
                                d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z" />
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        @if(session('info'))
            <div class="max-w-7xl mx-auto mt-4 px-4 sm:px-6 lg:px-8">
                <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg" role="alert">
                    {{ session('info') }}
                </div>
            </div>
        @endif

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>

    {{-- Bottom Navigation --}}
    @include('layouts.navigation')

    @livewireScripts
    @include('layouts.pwa')

    <script>
        (function () {
            // ── 1. Toasts para flash messages ──────────────────────────────────────
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });

            @if(session('success'))
                Toast.fire({ icon: 'success', title: @json(session('success')) });
            @endif

            @if(session('error'))
                Toast.fire({ icon: 'error', title: @json(session('error')) });
            @endif

            @if(session('info'))
                Toast.fire({ icon: 'info', title: @json(session('info')) });
            @endif

            @if(session('warning'))
                Toast.fire({ icon: 'warning', title: @json(session('warning')) });
            @endif

            // ── 2. Confirmación para forms con data-confirm ────────────────────────
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('form[data-confirm]').forEach(function (form) {
                    form.addEventListener('submit', function (e) {
                        e.preventDefault();

                        const message = form.dataset.confirm || '¿Estás seguro?';
                        const title = form.dataset.title || '¿Confirmar acción?';
                        const icon = form.dataset.icon || 'warning';
                        const btnText = form.dataset.btnText || 'Sí, continuar';
                        const btnColor = form.dataset.btnColor || '#ef4444';

                        Swal.fire({
                            title: title,
                            text: message,
                            icon: icon,
                            showCancelButton: true,
                            confirmButtonColor: btnColor,
                            cancelButtonColor: '#6b7280',
                            confirmButtonText: btnText,
                            cancelButtonText: 'Cancelar',
                            reverseButtons: true,
                            focusCancel: true,
                        }).then(function (result) {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    });
                });
            });
        })();
    </script>
</body>

</html>