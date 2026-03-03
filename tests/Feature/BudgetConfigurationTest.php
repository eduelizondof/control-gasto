<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\User;
use App\Models\BudgetConfiguration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BudgetConfigurationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_budget_configuration()
    {
        $user = User::factory()->create();
        $group = Group::create([
            'name' => 'Test Group',
            'created_by' => $user->id,
        ]);
        $group->users()->attach($user->id, ['role' => 'admin', 'is_active' => true]);

        $response = $this->actingAs($user)
            ->get(route('budget-configurations.index', $group));

        $response->assertStatus(200);
        $response->assertSee('Configuración del Presupuesto');
    }

    public function test_user_can_update_budget_configuration()
    {
        $user = User::factory()->create();
        $group = Group::create([
            'name' => 'Test Group',
            'created_by' => $user->id,
        ]);
        $group->users()->attach($user->id, ['role' => 'admin', 'is_active' => true]);

        $response = $this->actingAs($user)
            ->patch(route('budget-configurations.update', $group), [
                'necessities_percentage' => 40,
                'debts_percentage' => 30,
                'future_percentage' => 20,
                'desires_percentage' => 10,
            ]);

        $response->assertRedirect(route('budget-configurations.index', $group));
        $this->assertDatabaseHas('budget_configurations', [
            'group_id' => $group->id,
            'necessities_percentage' => 40.00,
        ]);
    }

    public function test_percentages_must_sum_to_100()
    {
        $user = User::factory()->create();
        $group = Group::create([
            'name' => 'Test Group',
            'created_by' => $user->id,
        ]);
        $group->users()->attach($user->id, ['role' => 'admin', 'is_active' => true]);

        $response = $this->actingAs($user)
            ->patch(route('budget-configurations.update', $group), [
                'necessities_percentage' => 50,
                'debts_percentage' => 50,
                'future_percentage' => 50,
                'desires_percentage' => 50,
            ]);

        $response->assertSessionHasErrors('total');
    }
}
