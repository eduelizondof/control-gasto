<div>
    @if($pendingInvitationsCount > 0)
        <div class="mb-6 bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-200 rounded-2xl p-4 flex items-center justify-between"
            x-data="{ show: true }" x-show="show" x-transition>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-indigo-800">
                        Tienes {{ $pendingInvitationsCount }} invitación{{ $pendingInvitationsCount > 1 ? 'es' : '' }}
                        pendiente{{ $pendingInvitationsCount > 1 ? 's' : '' }}
                    </p>
                    <p class="text-xs text-indigo-600">Te han invitado a unirte a un grupo</p>
                </div>
            </div>
            <a href="{{ route('invitations.index') }}"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition shadow-sm shrink-0">
                Ver invitaciones
            </a>
        </div>
    @endif
</div>