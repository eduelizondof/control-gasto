<div class="grid lg:grid-cols-3 gap-6 mb-8">
    {{-- 12-month Category Pie Chart --}}
    <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col" wire:ignore>
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
            </svg>
            Gastos por Categoría (últimos 12 meses)
        </h3>

        @if(count($chartCategoryData) > 0)
            <div class="relative flex-1 min-h-[300px]" x-data="{
                        init() {
                            const ctx = document.getElementById('categoryPieChart');
                            if (!ctx) return;

                            new Chart(ctx, {
                                type: 'doughnut',
                                data: {
                                    labels: @js($chartCategoryLabels),
                                    datasets: [{
                                        data: @js($chartCategoryData),
                                        backgroundColor: @js($chartCategoryColors),
                                        borderWidth: 2,
                                        borderColor: '#ffffff',
                                        hoverOffset: 4
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    cutout: '65%',
                                    plugins: {
                                        legend: {
                                            position: 'right',
                                            labels: {
                                                usePointStyle: true,
                                                pointStyle: 'circle',
                                                padding: 15,
                                                font: { family: 'Inter', size: 11 }
                                            }
                                        },
                                        tooltip: {
                                            backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                            titleFont: { family: 'Inter', size: 13 },
                                            bodyFont: { family: 'Inter', size: 12 },
                                            padding: 12,
                                            cornerRadius: 10,
                                            callbacks: {
                                                label: function(context) {
                                                    const value = context.parsed;
                                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                                    const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                                    return ` ${context.label}: $${value.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})} (${percentage}%)`;
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        }
                     }">
                <canvas id="categoryPieChart"></canvas>
            </div>
        @else
            <div
                class="flex-1 flex items-center justify-center min-h-[300px] bg-gray-50 rounded-xl border border-dashed border-gray-200">
                <p class="text-gray-400 text-sm">Sin datos suficientes para mostrar.</p>
            </div>
        @endif
    </div>

    {{-- Expenses This Month --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            Gastos Este Mes
        </h3>
        <div class="space-y-4 overflow-y-auto pr-2 flex-1 max-h-[300px]">
            @forelse($expensesThisMonth as $exp)
                @php
                    $percent = $monthlyExpenses > 0 ? round(($exp->total / $monthlyExpenses) * 100) : 0;
                @endphp
                <div>
                    <div class="flex items-center justify-between text-sm mb-1">
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="w-3 h-3 rounded-full shrink-0"
                                style="background-color: {{ $exp->category->color ?? '#9ca3af' }}"></div>
                            <span
                                class="font-medium text-gray-700 truncate">{{ $exp->category->name ?? 'Sin categoría' }}</span>
                        </div>
                        <span class="font-bold text-gray-900 shrink-0 ml-2">${{ number_format($exp->total, 2) }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                        <div class="h-1.5 rounded-full transition-all duration-300"
                            style="width: {{ $percent }}%; background-color: {{ $exp->category->color ?? '#9ca3af' }}">
                        </div>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1 text-right">{{ $percent }}% del gasto del mes</p>
                </div>
            @empty
                <div class="flex items-center justify-center h-full">
                    <p class="text-gray-400 text-sm text-center">Sin gastos registrados este mes.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>