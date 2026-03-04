<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Group;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class IncomeComparison extends Component
{
    public Group $group;

    public function placeholder()
    {
        return <<<'HTML'
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 animate-pulse">
            <div class="h-6 w-48 bg-gray-200 rounded mb-4"></div>
            <div class="h-32 w-full bg-gray-100 rounded-xl mb-4"></div>
            <div class="grid grid-cols-2 gap-3">
                <div class="h-12 bg-gray-50 rounded-xl"></div>
                <div class="h-12 bg-gray-50 rounded-xl"></div>
            </div>
        </div>
        HTML;
    }

    public function render()
    {
        $budgetConfig = $this->group->getBudgetConfiguration();
        $configuredIncome = (float) $budgetConfig->total_monthly_income;
        $configuredFixedIncome = (float) $budgetConfig->fixed_monthly_income;

        $twelveMonthsAgo = Carbon::now()->subMonths(11)->startOfMonth();

        $monthlyIncomeData = $this->group->transactions()
            ->confirmed()
            ->where('date', '>=', $twelveMonthsAgo)
            ->where('type', 'income')
            ->select(
                DB::raw("DATE_FORMAT(date, '%Y-%m') as period"),
                DB::raw("SUM(amount) as total")
            )
            ->groupBy('period')
            ->get();

        $totalIncomeSum = $monthlyIncomeData->sum('total');
        $monthsWithIncome = $monthlyIncomeData->count();

        $avgIncome12m = $monthsWithIncome > 0 ? $totalIncomeSum / $monthsWithIncome : 0;
        $incomeDiff = $configuredFixedIncome > 0 ? $avgIncome12m - $configuredFixedIncome : 0;
        $incomeDiffPercent = $configuredFixedIncome > 0 ? round(($incomeDiff / $configuredFixedIncome) * 100, 1) : 0;

        return view('livewire.dashboard.income-comparison', [
            'configuredIncome' => $configuredIncome,
            'configuredFixedIncome' => $configuredFixedIncome,
            'avgIncome12m' => $avgIncome12m,
            'incomeDiff' => $incomeDiff,
            'incomeDiffPercent' => $incomeDiffPercent,
        ]);
    }
}
