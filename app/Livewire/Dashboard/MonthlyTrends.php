<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Group;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MonthlyTrends extends Component
{
    public Group $group;

    public function placeholder()
    {
        return <<<'HTML'
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-100 animate-pulse">
            <div class="h-6 w-48 bg-gray-200 rounded mb-4"></div>
            <div class="h-[320px] w-full bg-gray-100 rounded-xl"></div>
        </div>
        HTML;
    }

    public function render()
    {
        $twelveMonthsAgo = Carbon::now()->subMonths(11)->startOfMonth();

        $monthlyData = $this->group->transactions()
            ->confirmed()
            ->where('date', '>=', $twelveMonthsAgo)
            ->select(
                DB::raw("DATE_FORMAT(date, '%Y-%m') as period"),
                DB::raw("SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as income"),
                DB::raw("SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expenses"),
                DB::raw("SUM(CASE WHEN type = 'savings' THEN amount ELSE 0 END) as savings")
            )
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->keyBy('period');

        $chartLabels = [];
        $chartIncome = [];
        $chartExpenses = [];
        $chartSavings = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $period = $date->format('Y-m');
            $chartLabels[] = $date->translatedFormat('M Y');

            $data = $monthlyData->get($period);
            $chartIncome[] = $data ? (float) $data->income : 0;
            $chartExpenses[] = $data ? (float) $data->expenses : 0;
            $chartSavings[] = $data ? (float) $data->savings : 0;
        }

        return view('livewire.dashboard.monthly-trends', [
            'chartLabels' => $chartLabels,
            'chartIncome' => $chartIncome,
            'chartExpenses' => $chartExpenses,
            'chartSavings' => $chartSavings,
        ]);
    }
}
