<div x-data="{ 
        open: false, 
        loading: false,
        txn: null,
        touchStartY: 0,
        
        async fetchTransaction(data) {
            const id = typeof data === 'object' ? data.id : data;
            const eventGroupId = typeof data === 'object' ? data.groupId : null;
            
            console.log('Transaction modal: received ID', id, 'Group ID', eventGroupId);
            
            this.loading = true;
            this.txn = null;
            this.open = true;
            
            try {
                const groupId = eventGroupId || '{{ isset($group) ? $group->id : "" }}' || (window.location.pathname.match(/groups\/(\d+)/)?.[1]);
                
                if (!groupId) {
                    throw new Error('Group ID not found');
                }

                const response = await fetch(`/groups/${groupId}/transactions/${id}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) throw new Error('Transaction not found');
                
                this.txn = await response.json();
            } catch (error) {
                console.error('Error fetching transaction:', error);
                this.closeModal();
            } finally {
                this.loading = false;
            }
        },

        closeModal() {
            this.open = false;
            setTimeout(() => { if (!this.open) this.txn = null; }, 300);
        },

        getIcon(iconName) {
            const icons = {
                'banknotes': '💵',
                'laptop': '💻',
                'chart-bar': '📊',
                'plus-circle': '➕',
                'home': '🏠',
                'shopping-cart': '🛒',
                'truck': '🚚',
                'heart': '❤️',
                'academic-cap': '🎓',
                'film': '🎬',
                'bolt': '⚡',
                'shopping-bag': '🛍️',
                'dots-horizontal': '💬',
                'shield-check': '🛡️',
                'currency-dollar': '💰',
                'trending-up': '📈',
                'switch-horizontal': '🔄',
                'cash': '💵',
                'credit-card': '💳'
            };
            return icons[iconName] || '📋';
        }
    }" @open-transaction-modal.window="fetchTransaction($event.detail)" x-cloak>

    {{-- Overlay backdrop --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-[60]"
        @click.self="closeModal()">

        {{-- Dark backdrop --}}
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="closeModal()"></div>

        {{-- Panel sliding from bottom --}}
        <div x-show="open" x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
            x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-y-0"
            x-transition:leave-end="translate-y-full"
            class="absolute bottom-0 md:bottom-auto md:top-1/2 md:-translate-y-1/2 md:left-1/2 md:-translate-x-1/2 left-0 right-0 md:w-full md:max-w-lg bg-gray-50 md:rounded-2xl rounded-t-3xl shadow-2xl overflow-hidden max-h-[85vh] flex flex-col"
            x-on:touchstart.passive="touchStartY = $event.touches[0].clientY"
            x-on:touchend.passive="if ($event.changedTouches[0].clientY - touchStartY > 80 && window.innerWidth < 768) closeModal()">

            {{-- Drag handle (Mobile only) --}}
            <div class="md:hidden flex justify-center pt-3 pb-2 cursor-grab bg-white">
                <div class="w-12 h-1.5 rounded-full bg-gray-300"></div>
            </div>

            {{-- Header --}}
            <div class="bg-white px-5 py-4 flex items-center justify-between border-b border-gray-100 shadow-sm z-10">
                <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                    Detalle del Movimiento
                </h3>
                <button @click="closeModal()"
                    class="text-gray-400 hover:text-gray-600 bg-gray-50 hover:bg-gray-100 rounded-full p-1.5 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="overflow-y-auto flex-1 p-5 space-y-6">

                {{-- Loading State --}}
                <template x-if="loading">
                    <div class="py-12 flex flex-col items-center justify-center space-y-4">
                        <div class="w-12 h-12 border-4 border-indigo-100 border-t-indigo-600 rounded-full animate-spin">
                        </div>
                        <p class="text-sm font-medium text-gray-400">Cargando detalles...</p>
                    </div>
                </template>

                {{-- Content --}}
                <div x-show="!loading && txn" x-transition>
                    {{-- Main Info --}}
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-3xl shadow-sm border border-gray-100 shrink-0"
                            :style="`background-color: ${txn?.categoryColor}15; color: ${txn?.categoryColor}`">
                            <template x-if="txn?.categoryIconUrl">
                                <img :src="txn.categoryIconUrl" class="w-8 h-8 object-contain opacity-80" alt="">
                            </template>
                            <template x-if="!txn?.categoryIconUrl">
                                <span x-text="getIcon(txn?.categoryIcon)"></span>
                            </template>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-gray-900 text-lg truncate" x-text="txn?.description"></p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-md"
                                    :style="`background-color: ${txn?.categoryColor}15; color: ${txn?.categoryColor}`"
                                    x-text="txn?.categoryName">
                                </span>
                                <span class="text-xs text-gray-500" x-text="txn?.date"></span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        {{-- Amount & Type --}}
                        <div
                            class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm flex justify-between items-center">
                            <div>
                                <p class="text-sm font-medium text-gray-500 mb-1">Monto</p>
                                <p class="text-2xl font-black"
                                    :class="txn?.type === 'income' ? 'text-emerald-600' : 'text-gray-900'"
                                    x-text="(txn?.type === 'income' ? '+' : '-') + '$' + txn?.amount">
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-500 mb-1">Tipo</p>
                                <span class="px-3 py-1 text-xs font-bold uppercase tracking-wider rounded-lg" :class="{
                                        'bg-emerald-100 text-emerald-700': txn?.type === 'income',
                                        'bg-rose-100 text-rose-700': txn?.type === 'expense',
                                        'bg-blue-100 text-blue-700': txn?.type === 'transfer',
                                        'bg-cyan-100 text-cyan-700': txn?.type === 'savings',
                                        'bg-gray-100 text-gray-700': txn?.type === 'adjustment'
                                    }" x-text="txn?.typeLabel"></span>
                            </div>
                        </div>

                        {{-- Details Grid --}}
                        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm space-y-4">
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Cuenta
                                    Origen</p>
                                <p class="text-sm font-medium text-gray-800" x-text="txn?.sourceAccount"></p>
                            </div>

                            <template x-if="txn?.destinationAccount">
                                <div class="pt-3 border-t border-gray-50">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Cuenta
                                        Destino</p>
                                    <p class="text-sm font-medium text-gray-800" x-text="txn?.destinationAccount"></p>
                                </div>
                            </template>

                            <template x-if="txn?.notes">
                                <div class="pt-3 border-t border-gray-50">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Notas /
                                        Detalles</p>
                                    <p class="text-sm text-gray-600 whitespace-pre-wrap" x-text="txn?.notes"></p>
                                </div>
                            </template>
                        </div>

                        {{-- Receipt Attachment --}}
                        <template x-if="txn?.receipt">
                            <div class="bg-indigo-50/50 rounded-2xl p-5 border border-indigo-100">
                                <p
                                    class="text-[10px] font-bold text-indigo-400 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13">
                                        </path>
                                    </svg>
                                    Comprobante Adjunto
                                </p>

                                <a :href="txn?.receipt" target="_blank"
                                    class="block group relative rounded-xl overflow-hidden border border-indigo-200 bg-white">
                                    <template x-if="txn?.receiptIsImage">
                                        <div class="aspect-video w-full bg-gray-100 flex items-center justify-center">
                                            <img :src="txn?.receipt" class="w-full h-full object-cover">
                                        </div>
                                    </template>
                                    <template x-if="!txn?.receiptIsImage">
                                        <div class="flex items-center gap-3 p-4">
                                            <div
                                                class="w-10 h-10 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                                    </path>
                                                </svg>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm font-semibold text-gray-800 truncate">Ver documento
                                                    adjunto</p>
                                                <p class="text-xs text-gray-500">Haz clic para abrir</p>
                                            </div>
                                        </div>
                                    </template>
                                    <div
                                        class="absolute inset-0 bg-indigo-900/10 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <span
                                            class="bg-white/90 backdrop-blur-sm text-indigo-700 text-xs font-bold px-3 py-1.5 rounded-full shadow-sm">Abrir
                                            archivo</span>
                                    </div>
                                </a>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Edit Button Footer --}}
            <template x-if="!loading && txn">
                <div class="p-4 bg-white border-t border-gray-100 pb-safe">
                    <a :href="txn?.editUrl"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-xl text-sm font-bold transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>
                        Editar Movimiento
                    </a>
                </div>
            </template>
        </div>
    </div>
</div>