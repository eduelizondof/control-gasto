<?php

namespace App\Livewire\Dashboard;

use App\Models\Group;
use App\Models\PaymentCalendar;
use Carbon\Carbon;
use Livewire\Component;

class UpcomingPaymentCalendar extends Component
{
    public Group $group;

    public function mount(Group $group)
    {
        $this->group = $group;
    }

    public function getUpcomingQuincenasProperty()
    {
        $now = Carbon::now();
        // Mostrar quincenas desde el inicio de este mes hasta el final del mes siguiente
        $start = $now->copy()->startOfMonth();
        $end = $now->copy()->endOfMonth(); // Solo mostrar quincenas del mes actual

        return PaymentCalendar::where('group_id', $this->group->id)
            ->whereNull('transaction_id')
            ->whereBetween('payment_date', [$start, $end])
            ->orderBy('payment_date')
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard.upcoming-payment-calendar');
    }
}
