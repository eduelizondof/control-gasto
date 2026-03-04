<div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-100" wire:ignore>
    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
        </svg>
        Ingresos vs Gastos vs Ahorro
    </h3>
    <div class="relative" style="height: 320px;" x-data="{
            init() {
                const ctx = document.getElementById('monthlyChart');
                if (!ctx) return;
                
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: @js($chartLabels),
                        datasets: [
                            {
                                label: 'Ingresos',
                                data: @js($chartIncome),
                                backgroundColor: 'rgba(16, 185, 129, 0.8)',
                                borderColor: 'rgb(16, 185, 129)',
                                borderWidth: 1,
                                borderRadius: 6,
                            },
                            {
                                label: 'Gastos',
                                data: @js($chartExpenses),
                                backgroundColor: 'rgba(244, 63, 94, 0.8)',
                                borderColor: 'rgb(244, 63, 94)',
                                borderWidth: 1,
                                borderRadius: 6,
                            },
                            {
                                label: 'Ahorro',
                                data: @js($chartSavings),
                                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                                borderColor: 'rgb(59, 130, 246)',
                                borderWidth: 1,
                                borderRadius: 6,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            intersect: false,
                            mode: 'index',
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    usePointStyle: true,
                                    pointStyle: 'rectRounded',
                                    padding: 20,
                                    font: { family: 'Inter', size: 12, weight: '600' }
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
                                        return context.dataset.label + ': $' + 
                                            context.parsed.y.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: {
                                    font: { family: 'Inter', size: 10 },
                                    maxRotation: 45,
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: { color: 'rgba(0,0,0,0.05)' },
                                ticks: {
                                    font: { family: 'Inter', size: 11 },
                                    callback: function(value) {
                                        return '$' + value.toLocaleString('en-US');
                                    }
                                }
                            }
                        }
                    }
                });
            }
         }">
        <canvas id="monthlyChart"></canvas>
    </div>
</div>