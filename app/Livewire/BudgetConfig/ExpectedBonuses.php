<?php

namespace App\Livewire\BudgetConfig;

use App\Models\ExpectedBonus;
use App\Models\Group;
use Livewire\Component;
use Livewire\Attributes\Computed;

class ExpectedBonuses extends Component
{
    public Group $group;

    public $bonusId = null;
    public $name = '';
    public $amount = '';
    public $month = '';
    public $day = '';
    public $is_active = true;

    public $isFormOpen = false;

    protected $rules = [
        'name' => 'required|string|max:150',
        'amount' => 'required|numeric|min:0',
        'month' => 'required|integer|min:1|max:12',
        'day' => 'nullable|integer|min:1|max:31',
        'is_active' => 'boolean',
    ];

    public function mount(Group $group)
    {
        $this->group = $group;
    }

    #[Computed]
    public function bonuses()
    {
        return $this->group->expectedBonuses()->orderBy('month')->orderBy('day')->get();
    }

    public function openForm($id = null)
    {
        \Illuminate\Support\Facades\Log::info('openForm button clicked in Livewire', ['id' => $id]);
        $this->resetValidation();
        if ($id) {
            $bonus = ExpectedBonus::findOrFail($id);
            $this->bonusId = $bonus->id;
            $this->name = $bonus->name;
            $this->amount = $bonus->amount;
            $this->month = $bonus->month;
            $this->day = $bonus->day;
            $this->is_active = $bonus->is_active;
        } else {
            $this->resetForm();
        }
        $this->isFormOpen = true;
    }

    public function closeForm()
    {
        $this->isFormOpen = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->bonusId = null;
        $this->name = '';
        $this->amount = '';
        $this->month = '';
        $this->day = '';
        $this->is_active = true;
    }

    public function save()
    {
        $this->validate();

        ExpectedBonus::updateOrCreate(
            ['id' => $this->bonusId, 'group_id' => $this->group->id],
            [
                'name' => $this->name,
                'amount' => $this->amount,
                'month' => $this->month,
                'day' => $this->day ?: null,
                'is_active' => $this->is_active,
            ]
        );

        $this->closeForm();
        
        $this->dispatchBonusesUpdate();

        session()->flash('success', 'Bono guardado exitosamente.');
    }

    public function delete($id)
    {
        ExpectedBonus::findOrFail($id)->delete();
        $this->dispatchBonusesUpdate();
        session()->flash('success', 'Bono eliminado correctamente.');
    }

    private function dispatchBonusesUpdate()
    {
        $totalBonuses = $this->group->expectedBonuses()->where('is_active', true)->sum('amount');
        $this->dispatch('bonuses-updated', monthlyBonus: $totalBonuses / 12);
    }

    public function render()
    {
        return view('livewire.budget-config.expected-bonuses');
    }
}
