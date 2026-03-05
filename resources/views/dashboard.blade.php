<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 leading-tight">
                    {{ $group->name }} — <span
                        class="capitalize">{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</span>
                </h2>
                <p class="text-sm text-gray-500 mt-1">Panel de Control</p>
            </div>
            <a href="{{ route('transactions.create', $group) }}"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition shadow-sm">
                + Nuevo Movimiento
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Pending Invitations --}}
            <livewire:dashboard.pending-invitations />

            <!-- Summary Cards -->
            <livewire:dashboard.summary-cards :group="$group" />

            <!-- Budget vs Expenses + Upcoming Big Payments -->
            <div class="grid lg:grid-cols-3 gap-6 mb-8">
                <!-- Budget Overview -->
                <div class="lg:col-span-2 h-full">
                    <livewire:dashboard.budget-overview :group="$group" />
                </div>

                <!-- Big Payments, Bonuses & Payment Calendar -->
                <div class="space-y-6 flex flex-col h-full">
                    <livewire:dashboard.upcoming-big-payments :group="$group" />
                    <livewire:dashboard.upcoming-payment-calendar :group="$group" />
                </div>
            </div>

            <!-- Accounts -->
            <div class="mb-8 h-full">
                <livewire:dashboard.account-list :group="$group" />
            </div>

            <!-- Quincena Analysis & Savings Tips -->
            <livewire:dashboard.quincena-analysis :group="$group" />

            <!-- Recent Transactions -->
            <livewire:dashboard.recent-activity :group="$group" />

            {{-- Income Comparison & Monthly Chart (Lazy Loading) --}}
            <div class="grid lg:grid-cols-3 gap-6 mb-8">
                {{-- Income Comparison --}}
                <livewire:dashboard.income-comparison :group="$group" lazy />

                {{-- Monthly Bar Chart --}}
                <livewire:dashboard.monthly-trends :group="$group" lazy />
            </div>

            {{-- Expenses By Category (Lazy Loading) --}}
            <livewire:dashboard.category-distribution :group="$group" lazy />

            <!-- Active Debts -->
            <livewire:dashboard.active-debts :group="$group" />
        </div>
    </div>

    {{-- Chart.js is required for the charts components --}}
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @endpush
</x-app-layout>