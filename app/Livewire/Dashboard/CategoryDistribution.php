<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Group;
use Carbon\Carbon;

class CategoryDistribution extends Component
{
    public Group $group;

    public function placeholder()
    {
        return <<<'HTML'
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-100 animate-pulse flex flex-col">
            <div class="h-6 w-64 bg-gray-200 rounded mb-4"></div>
            <div class="flex-1 min-h-[300px] w-full bg-gray-100 rounded-xl"></div>
        </div>
        HTML;
    }

    public function render()
    {
        $twelveMonthsAgo = Carbon::now()->subMonths(11)->startOfMonth();

        $expensesByCategory12m = $this->group->transactions()
            ->confirmed()
            ->where('date', '>=', $twelveMonthsAgo)
            ->ofType('expense')
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->with('category')
            ->get()
            ->sortByDesc('total');

        $chartCategoryLabels = [];
        $chartCategoryData = [];
        $chartCategoryColors = [];

        foreach ($expensesByCategory12m as $exp) {
            $chartCategoryLabels[] = $exp->category->name ?? 'Sin categoría';
            $chartCategoryData[] = (float) $exp->total;
            $chartCategoryColors[] = $exp->category->color ?? '#9ca3af';
        }

        // Monthly specific data for the sidebar list
        $monthlyExpenses = $this->group->transactions()
            ->confirmed()
            ->thisMonth()
            ->ofType('expense')
            ->sum('amount');

        $expensesThisMonth = $this->group->transactions()
            ->confirmed()
            ->thisMonth()
            ->ofType('expense')
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->with('category')
            ->get()
            ->sortByDesc('total');

        return view('livewire.dashboard.category-distribution', [
            'chartCategoryLabels' => $chartCategoryLabels,
            'chartCategoryData' => $chartCategoryData,
            'chartCategoryColors' => $chartCategoryColors,
            'monthlyExpenses' => $monthlyExpenses,
            'expensesThisMonth' => $expensesThisMonth,
        ]);
    }
}
