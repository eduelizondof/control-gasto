<!-- PWA Add to Home Screen Prompt -->
<div id="pwa-prompt"
    class="fixed bottom-4 left-4 right-4 md:left-auto md:right-4 md:w-96 bg-white shadow-2xl rounded-2xl p-5 border border-gray-100 z-50 transform translate-y-full opacity-0 pointer-events-none transition-all duration-500 flex flex-col gap-3 pb-8 md:pb-5">

    <!-- Header -->
    <div class="flex items-start justify-between">
        <div class="flex items-center gap-4">
            <div
                class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center flex-shrink-0 border border-indigo-100">
                <img src="https://conectivaits.com/images/logo/logo-icono-color.png" alt="Icon"
                    class="w-8 h-8 object-contain">
            </div>
            <div>
                <h3 class="font-bold text-gray-900 text-sm">Instala la App</h3>
                <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">Añade ConectaTusFinanzas al inicio para un
                    acceso más rápido.</p>
            </div>
        </div>
        <button id="pwa-closeBtn" class="text-gray-400 hover:text-gray-600 transition-colors p-1" aria-label="Cerrar">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Actions -->
    <div class="mt-2 flex items-center justify-end gap-2">
        <button id="pwa-installBtn"
            class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm py-2 px-4 rounded-xl shadow-sm transition-all shadow-indigo-200">
            Añadir al inicio
        </button>
    </div>

    <!-- iOS Instructions (Hidden by default) -->
    <div id="pwa-ios-instructions"
        class="hidden mt-3 text-xs text-center text-gray-500 bg-gray-50 p-3 rounded-lg border border-gray-100">
        Pulsa el botón
        <svg class="w-4 h-4 inline-block mx-1 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
        </svg>
        Compartir y selecciona <strong>"Añadir a la pantalla de inicio"</strong>.
    </div>
</div>

<script>
    // Register Service Worker
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js').then(registration => {
                console.log('SW registered:', registration);
            }).catch(error => {
                console.log('SW registration failed:', error);
            });
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        const promptEl = document.getElementById('pwa-prompt');
        const installBtn = document.getElementById('pwa-installBtn');
        const closeBtn = document.getElementById('pwa-closeBtn');
        const iosInstructions = document.getElementById('pwa-ios-instructions');

        // PWA Prompt Logic
        let deferredPrompt;

        // Check if already dismissed
        const isDismissed = localStorage.getItem('pwaPromptDeclined') === 'true';

        // 1. Android / Chrome Detection via beforeinstallprompt
        window.addEventListener('beforeinstallprompt', (e) => {
            // Prevent Chrome 67 and earlier from automatically showing the prompt
            e.preventDefault();
            // Stash the event so it can be triggered later.
            deferredPrompt = e;

            if (!isDismissed) {
                showPrompt();
            }
        });

        // 2. iOS Safari Detection
        const isIos = () => {
            const userAgent = window.navigator.userAgent.toLowerCase();
            return /iphone|ipad|ipod/.test(userAgent);
        };

        const isInStandaloneMode = () => ('standalone' in window.navigator) && (window.navigator.standalone);

        // Show prompt for iOS if not installed & not dismissed
        if (isIos() && !isInStandaloneMode() && !isDismissed) {
            installBtn.style.display = 'none';
            iosInstructions.classList.remove('hidden');
            // Adding a small delay to not overlap with splash screens
            setTimeout(() => {
                showPrompt();
            }, 2000);
        }

        // Functions
        function showPrompt() {
            promptEl.classList.remove('translate-y-full', 'opacity-0', 'pointer-events-none');
            promptEl.classList.add('translate-y-0', 'opacity-100', 'pointer-events-auto');
        }

        function hidePrompt() {
            promptEl.classList.add('translate-y-full', 'opacity-0', 'pointer-events-none');
            promptEl.classList.remove('translate-y-0', 'opacity-100', 'pointer-events-auto');
        }

        // Event Listeners
        closeBtn.addEventListener('click', () => {
            hidePrompt();
            localStorage.setItem('pwaPromptDeclined', 'true');
        });

        installBtn.addEventListener('click', async () => {
            hidePrompt();
            if (deferredPrompt) {
                // Show the install prompt
                deferredPrompt.prompt();
                // Wait for the user to respond to the prompt
                const { outcome } = await deferredPrompt.userChoice;
                if (outcome === 'accepted') {
                    console.log('User accepted the A2HS prompt');
                } else {
                    console.log('User dismissed the A2HS prompt');
                    localStorage.setItem('pwaPromptDeclined', 'true');
                }
                deferredPrompt = null;
            }
        });
    });
</script>