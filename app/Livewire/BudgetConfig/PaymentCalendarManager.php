<?php

namespace App\Livewire\BudgetConfig;

use App\Models\Account;
use App\Models\Group;
use App\Models\PaymentCalendar;
use Carbon\Carbon;
use Livewire\Component;

class PaymentCalendarManager extends Component
{
    public Group $group;

    public $personName = '';
    public $accountId = '';
    public $categoryId = '';
    public $conceptId = '';

    public $calendarEntries = [];
    public $isEditing = false;
    public $editingPerson = null;
    public $defaultAmount = '';

    protected $rules = [
        'personName' => 'required|string|max:255',
        'accountId' => 'required|exists:accounts,id',
        'categoryId' => 'required|exists:categories,id',
        'conceptId' => 'nullable|exists:concepts,id',
        'calendarEntries' => 'required|array|min:1',
        'calendarEntries.*.payment_date' => 'required|date',
        'calendarEntries.*.amount' => 'required|numeric|min:0.01',
        'calendarEntries.*.concept' => 'required|string|max:255',
    ];

    public function mount(Group $group)
    {
        $this->group = $group;
    }

    public function generateDefaults()
    {
        $this->validate([
            'personName' => 'required|string|max:255',
            'accountId' => 'required|exists:accounts,id',
            'categoryId' => 'required|exists:categories,id',
            'conceptId' => 'nullable|exists:concepts,id',
            'defaultAmount' => 'required|numeric|min:0.01',
        ], [
            'defaultAmount.required' => 'Debes ingresar cuánto ganas por quincena para calcular el resto.',
            'defaultAmount.numeric' => 'El monto debe ser un número.',
        ]);

        $year = Carbon::now()->year;
        $entries = [];

        $months = [
            1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr',
            5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago',
            9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic'
        ];

        for ($month = 1; $month <= 12; $month++) {
            $date1 = Carbon::createFromDate($year, $month, 15);
            $entries[] = [
                'payment_date' => $this->adjustToWeekday($date1)->format('Y-m-d'),
                'amount' => $this->defaultAmount,
                'concept' => "Quincena 1 {$months[$month]}",
            ];

            $date2 = Carbon::createFromDate($year, $month, 1)->endOfMonth();
            $entries[] = [
                'payment_date' => $this->adjustToWeekday($date2)->format('Y-m-d'),
                'amount' => $this->defaultAmount,
                'concept' => "Quincena 2 {$months[$month]}",
            ];
        }

        $this->calendarEntries = $entries;
        $this->isEditing = true;
    }

    private function adjustToWeekday(Carbon $date)
    {
        if ($date->isWeekend()) {
            return $date->previous(Carbon::FRIDAY);
        }
        return $date;
    }

    public function addEntry()
    {
        $this->calendarEntries[] = [
            'payment_date' => Carbon::now()->format('Y-m-d'),
            'amount' => '',
            'concept' => '',
        ];
    }

    public function removeEntry($index)
    {
        unset($this->calendarEntries[$index]);
        $this->calendarEntries = array_values($this->calendarEntries);
    }

    public function saveCalendar()
    {
        $this->validate();

        if ($this->editingPerson) {
            PaymentCalendar::where('group_id', $this->group->id)
                ->where('person_name', $this->editingPerson)
                ->delete();
        }

        foreach ($this->calendarEntries as $entry) {
            PaymentCalendar::create([
                'group_id' => $this->group->id,
                'account_id' => $this->accountId,
                'person_name' => $this->personName,
                'concept' => $entry['concept'],
                'amount' => $entry['amount'],
                'payment_date' => $entry['payment_date'],
                'category_id' => $this->categoryId,
                'concept_id' => $this->conceptId ?: null,
            ]);
        }

        $this->resetForm();
        session()->flash('success', 'Calendario guardado correctamente.');
    }

    public function editPerson($personName)
    {
        $entries = PaymentCalendar::where('group_id', $this->group->id)
            ->where('person_name', $personName)
            ->orderBy('payment_date')
            ->get();

        if ($entries->isNotEmpty()) {
            $this->personName = $personName;
            $this->accountId = $entries->first()->account_id;
            $this->categoryId = $entries->first()->category_id;
            $this->conceptId = $entries->first()->concept_id;

            $this->calendarEntries = $entries->map(function ($entry) {
                return [
                    'payment_date' => $entry->payment_date->format('Y-m-d'),
                    'amount' => $entry->amount,
                    'concept' => $entry->concept,
                ];
            })->toArray();
            
            $this->editingPerson = $personName;
            $this->isEditing = true;
        }
    }

    public function deletePerson($personName)
    {
        PaymentCalendar::where('group_id', $this->group->id)
            ->where('person_name', $personName)
            ->delete();
            
        session()->flash('success', 'Calendario de pagos eliminado para ' . $personName);
    }

    public function resetForm()
    {
        $this->personName = '';
        $this->accountId = '';
        $this->categoryId = '';
        $this->conceptId = '';
        $this->calendarEntries = [];
        $this->isEditing = false;
        $this->editingPerson = null;
        $this->defaultAmount = '';
    }

    public function getAccountsProperty()
    {
        return Account::where('group_id', $this->group->id)
            ->active()
            ->orderBy('sort_order')
            ->get();
    }

    public function getGroupUsersProperty()
    {
        return $this->group->users()->get();
    }

    public function getExistingCalendarsProperty()
    {
        return PaymentCalendar::where('group_id', $this->group->id)
            ->orderBy('payment_date')
            ->get()
            ->groupBy('person_name');
    }

    public function getCategoriesProperty()
    {
        return $this->group->categories()->orderBy('name')->get();
    }

    public function getConceptsProperty()
    {
        return $this->group->concepts()->orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.budget-config.payment-calendar-manager');
    }
}
