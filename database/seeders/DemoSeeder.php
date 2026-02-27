<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\BudgetItem;
use App\Models\Category;
use App\Models\Concept;
use App\Models\Debt;
use App\Models\DebtLimit;
use App\Models\Group;
use App\Models\MonthlyBudget;
use App\Models\Reminder;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Create demo user ──
        $user = User::create([
            'name' => 'Usuario Demo',
            'email' => 'demo@conectatusfinanzas.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // ── 2. Create demo group ──
        $group = Group::create([
            'name' => 'Mi Familia',
            'description' => 'Grupo familiar principal para gestión de gastos',
            'created_by' => $user->id,
        ]);

        $group->users()->attach($user->id, [
            'role' => 'admin',
            'joined_at' => now(),
            'is_active' => true,
        ]);

        // ── 3. Create system categories ──
        $categories = $this->createCategories($group->id);

        // ── 4. Create concepts ──
        $concepts = $this->createConcepts($group->id, $categories);

        // ── 5. Create accounts ──
        $accounts = $this->createAccounts($group->id);

        // ── 6. Create monthly budget ──
        $this->createBudget($group->id, $categories, $concepts, $accounts);

        // ── 7. Create sample transactions ──
        $this->createTransactions($group->id, $user->id, $categories, $concepts, $accounts);

        // ── 8. Create debts ──
        $this->createDebts($group->id, $accounts);

        // ── 9. Create debt limit ──
        DebtLimit::create([
            'group_id' => $group->id,
            'name' => 'Límite de endeudamiento personal',
            'max_amount' => 50000,
            'committed_amount' => 28000,
            'available_amount' => 22000,
        ]);

        // ── 10. Create reminders ──
        $this->createReminders($group->id, $accounts, $concepts);
    }

    private function createCategories(int $groupId): array
    {
        $data = [
            'income' => [
                ['name' => 'Salario', 'icon' => 'banknotes', 'color' => '#10B981'],
                ['name' => 'Freelance', 'icon' => 'laptop', 'color' => '#06B6D4'],
                ['name' => 'Inversiones', 'icon' => 'chart-bar', 'color' => '#8B5CF6'],
                ['name' => 'Otros ingresos', 'icon' => 'plus-circle', 'color' => '#14B8A6'],
            ],
            'expense' => [
                ['name' => 'Vivienda', 'icon' => 'home', 'color' => '#EF4444'],
                ['name' => 'Alimentación', 'icon' => 'shopping-cart', 'color' => '#F97316'],
                ['name' => 'Transporte', 'icon' => 'truck', 'color' => '#F59E0B'],
                ['name' => 'Salud', 'icon' => 'heart', 'color' => '#EC4899'],
                ['name' => 'Educación', 'icon' => 'academic-cap', 'color' => '#6366F1'],
                ['name' => 'Entretenimiento', 'icon' => 'film', 'color' => '#A855F7'],
                ['name' => 'Servicios', 'icon' => 'bolt', 'color' => '#3B82F6'],
                ['name' => 'Ropa', 'icon' => 'shopping-bag', 'color' => '#D946EF'],
                ['name' => 'Otros gastos', 'icon' => 'dots-horizontal', 'color' => '#6B7280'],
            ],
            'savings' => [
                ['name' => 'Fondo de emergencia', 'icon' => 'shield-check', 'color' => '#059669'],
                ['name' => 'Ahorro general', 'icon' => 'currency-dollar', 'color' => '#0D9488'],
                ['name' => 'Inversión', 'icon' => 'trending-up', 'color' => '#7C3AED'],
            ],
            'transfer' => [
                ['name' => 'Transferencia entre cuentas', 'icon' => 'switch-horizontal', 'color' => '#64748B'],
            ],
        ];

        $categories = [];
        $order = 0;

        foreach ($data as $type => $items) {
            foreach ($items as $item) {
                $cat = Category::create([
                    'group_id' => $groupId,
                    'name' => $item['name'],
                    'type' => $type,
                    'color' => $item['color'],
                    'icon' => $item['icon'],
                    'is_system' => true,
                    'sort_order' => $order++,
                ]);
                $categories[$item['name']] = $cat;
            }
        }

        return $categories;
    }

    private function createConcepts(int $groupId, array $categories): array
    {
        $data = [
            'Salario' => ['Quincena 1', 'Quincena 2', 'Aguinaldo'],
            'Alimentación' => ['Supermercado', 'Comida rápida', 'Frutas y verduras'],
            'Transporte' => ['Gasolina', 'Uber / Taxi', 'Estacionamiento'],
            'Servicios' => ['Electricidad CFE', 'Agua', 'Internet', 'Netflix', 'Spotify'],
            'Vivienda' => ['Renta', 'Mantenimiento hogar'],
        ];

        $concepts = [];

        foreach ($data as $categoryName => $items) {
            if (!isset($categories[$categoryName])) {
                continue;
            }

            foreach ($items as $name) {
                $concept = Concept::create([
                    'group_id' => $groupId,
                    'category_id' => $categories[$categoryName]->id,
                    'name' => $name,
                    'is_system' => true,
                ]);
                $concepts[$name] = $concept;
            }
        }

        return $concepts;
    }

    private function createAccounts(int $groupId): array
    {
        $accounts = [];

        $accounts['cash'] = Account::create([
            'group_id' => $groupId,
            'name' => 'Efectivo',
            'type' => 'cash',
            'currency' => 'MXN',
            'initial_balance' => 2500,
            'current_balance' => 2500,
            'color' => '#10B981',
            'icon' => 'cash',
            'sort_order' => 0,
        ]);

        $accounts['debit'] = Account::create([
            'group_id' => $groupId,
            'name' => 'BBVA Débito',
            'type' => 'debit',
            'bank' => 'BBVA',
            'currency' => 'MXN',
            'initial_balance' => 15000,
            'current_balance' => 15000,
            'color' => '#3B82F6',
            'icon' => 'credit-card',
            'sort_order' => 1,
        ]);

        $accounts['credit'] = Account::create([
            'group_id' => $groupId,
            'name' => 'BBVA Crédito',
            'type' => 'credit',
            'bank' => 'BBVA',
            'currency' => 'MXN',
            'initial_balance' => 0,
            'current_balance' => 0,
            'credit_limit' => 30000,
            'cutoff_day' => 15,
            'payment_day' => 5,
            'color' => '#8B5CF6',
            'icon' => 'credit-card',
            'sort_order' => 2,
        ]);

        return $accounts;
    }

    private function createBudget(int $groupId, array $categories, array $concepts, array $accounts): void
    {
        $budget = MonthlyBudget::create([
            'group_id' => $groupId,
            'name' => 'Presupuesto Base 2025',
            'is_active' => true,
        ]);

        $items = [
            ['concept' => 'Renta', 'category' => 'Vivienda', 'amount' => 8000, 'freq' => 'monthly', 'fixed' => true],
            ['concept' => 'Supermercado', 'category' => 'Alimentación', 'amount' => 4000, 'freq' => 'monthly', 'fixed' => false],
            ['concept' => 'Gasolina', 'category' => 'Transporte', 'amount' => 2000, 'freq' => 'monthly', 'fixed' => false],
            ['concept' => 'Electricidad CFE', 'category' => 'Servicios', 'amount' => 1200, 'freq' => 'bimonthly', 'fixed' => false],
            ['concept' => 'Internet', 'category' => 'Servicios', 'amount' => 699, 'freq' => 'monthly', 'fixed' => true],
            ['concept' => 'Netflix', 'category' => 'Servicios', 'amount' => 299, 'freq' => 'monthly', 'fixed' => true],
            ['concept' => 'Spotify', 'category' => 'Servicios', 'amount' => 179, 'freq' => 'monthly', 'fixed' => true],
            ['concept' => null, 'custom_name' => 'Seguro de auto', 'category' => 'Transporte', 'amount' => 12000, 'freq' => 'annual', 'fixed' => true],
        ];

        $order = 0;
        foreach ($items as $item) {
            $conceptId = isset($item['concept']) && isset($concepts[$item['concept']]) ? $concepts[$item['concept']]->id : null;
            $categoryId = $categories[$item['category']]->id;
            $divisor = BudgetItem::frequencyDivisor($item['freq']);

            BudgetItem::create([
                'monthly_budget_id' => $budget->id,
                'concept_id' => $conceptId,
                'custom_name' => $item['custom_name'] ?? null,
                'category_id' => $categoryId,
                'estimated_amount' => $item['amount'],
                'frequency' => $item['freq'],
                'monthly_amount' => round($item['amount'] / $divisor, 2),
                'account_id' => $accounts['debit']->id,
                'is_fixed' => $item['fixed'],
                'sort_order' => $order++,
            ]);
        }
    }

    private function createTransactions(int $groupId, int $userId, array $categories, array $concepts, array $accounts): void
    {
        $now = now();

        $txns = [
            // Incomes
            ['date' => $now->copy()->day(1), 'desc' => 'Quincena 1 Febrero', 'cat' => 'Salario', 'type' => 'income', 'account' => 'debit', 'amount' => 15000, 'concept' => 'Quincena 1'],
            ['date' => $now->copy()->day(15), 'desc' => 'Quincena 2 Febrero', 'cat' => 'Salario', 'type' => 'income', 'account' => 'debit', 'amount' => 15000, 'concept' => 'Quincena 2'],

            // Expenses
            ['date' => $now->copy()->day(1), 'desc' => 'Pago de renta', 'cat' => 'Vivienda', 'type' => 'expense', 'account' => 'debit', 'amount' => 8000, 'concept' => 'Renta'],
            ['date' => $now->copy()->day(3), 'desc' => 'Despensa semanal', 'cat' => 'Alimentación', 'type' => 'expense', 'account' => 'debit', 'amount' => 1200, 'concept' => 'Supermercado'],
            ['date' => $now->copy()->day(5), 'desc' => 'Tanque de gasolina', 'cat' => 'Transporte', 'type' => 'expense', 'account' => 'debit', 'amount' => 800, 'concept' => 'Gasolina'],
            ['date' => $now->copy()->day(7), 'desc' => 'Pago Netflix', 'cat' => 'Servicios', 'type' => 'expense', 'account' => 'credit', 'amount' => 299, 'concept' => 'Netflix'],
            ['date' => $now->copy()->day(8), 'desc' => 'Comida rápida', 'cat' => 'Alimentación', 'type' => 'expense', 'account' => 'cash', 'amount' => 350, 'concept' => 'Comida rápida'],
            ['date' => $now->copy()->day(10), 'desc' => 'Pago Internet', 'cat' => 'Servicios', 'type' => 'expense', 'account' => 'debit', 'amount' => 699, 'concept' => 'Internet'],
            ['date' => $now->copy()->day(12), 'desc' => 'Uber al trabajo', 'cat' => 'Transporte', 'type' => 'expense', 'account' => 'debit', 'amount' => 180, 'concept' => 'Uber / Taxi'],

            // Savings
            ['date' => $now->copy()->day(2), 'desc' => 'Ahorro quincenal', 'cat' => 'Ahorro general', 'type' => 'savings', 'account' => 'debit', 'amount' => 3000, 'concept' => null, 'dest' => 'cash'],
        ];

        foreach ($txns as $txn) {
            $categoryId = $categories[$txn['cat']]->id;
            $conceptId = isset($txn['concept']) && isset($concepts[$txn['concept']]) ? $concepts[$txn['concept']]->id : null;
            $sourceAccountId = $accounts[$txn['account']]->id;
            $destAccountId = isset($txn['dest']) ? $accounts[$txn['dest']]->id : null;

            $transaction = Transaction::create([
                'group_id' => $groupId,
                'user_id' => $userId,
                'date' => $txn['date']->format('Y-m-d'),
                'time' => $txn['date']->format('H:i:s'),
                'description' => $txn['desc'],
                'category_id' => $categoryId,
                'concept_id' => $conceptId,
                'type' => $txn['type'],
                'source_account_id' => $sourceAccountId,
                'destination_account_id' => $destAccountId,
                'amount' => $txn['amount'],
                'status' => 'confirmed',
                'source' => 'manual',
            ]);

            // Update balances
            match ($txn['type']) {
                'income' => $accounts[$txn['account']]->increment('current_balance', $txn['amount']),
                'expense' => $accounts[$txn['account']]->decrement('current_balance', $txn['amount']),
                'savings', 'transfer' => (function () use ($accounts, $txn) {
                    $accounts[$txn['account']]->decrement('current_balance', $txn['amount']);
                    if (isset($txn['dest'])) {
                        $accounts[$txn['dest']]->increment('current_balance', $txn['amount']);
                    }
                })(),
                default => null,
            };
        }
    }

    private function createDebts(int $groupId, array $accounts): void
    {
        Debt::create([
            'group_id' => $groupId,
            'account_id' => $accounts['credit']->id,
            'name' => 'Laptop MSI - MSI sin intereses',
            'type' => 'no_interest_installments',
            'total_amount' => 18000,
            'paid_amount' => 6000,
            'outstanding_balance' => 12000,
            'interest_rate' => 0,
            'total_payments' => 12,
            'payments_made' => 4,
            'payment_amount' => 1500,
            'start_date' => now()->subMonths(4),
            'end_date' => now()->addMonths(8),
            'next_payment_date' => now()->addDays(15),
            'status' => 'active',
            'notes' => '12 MSI en BBVA crédito',
        ]);

        Debt::create([
            'group_id' => $groupId,
            'account_id' => $accounts['debit']->id,
            'name' => 'Préstamo familiar',
            'type' => 'personal_loan',
            'total_amount' => 10000,
            'paid_amount' => 4000,
            'outstanding_balance' => 6000,
            'interest_rate' => 0,
            'total_payments' => 5,
            'payments_made' => 2,
            'payment_amount' => 2000,
            'start_date' => now()->subMonths(2),
            'end_date' => now()->addMonths(3),
            'next_payment_date' => now()->startOfMonth()->addMonth(),
            'status' => 'active',
        ]);
    }

    private function createReminders(int $groupId, array $accounts, array $concepts): void
    {
        Reminder::create([
            'group_id' => $groupId,
            'name' => 'Pago de renta',
            'type' => 'fixed_payment',
            'account_id' => $accounts['debit']->id,
            'concept_id' => $concepts['Renta']->id ?? null,
            'estimated_amount' => 8000,
            'frequency' => 'monthly',
            'day_of_month' => 1,
            'advance_days' => 3,
            'is_active' => true,
            'next_date' => now()->addMonth()->startOfMonth(),
        ]);

        Reminder::create([
            'group_id' => $groupId,
            'name' => 'Pago Internet Telmex',
            'type' => 'fixed_payment',
            'account_id' => $accounts['debit']->id,
            'concept_id' => $concepts['Internet']->id ?? null,
            'estimated_amount' => 699,
            'frequency' => 'monthly',
            'day_of_month' => 10,
            'advance_days' => 2,
            'is_active' => true,
            'next_date' => now()->addMonth()->day(10),
        ]);

        Reminder::create([
            'group_id' => $groupId,
            'name' => 'Corte tarjeta BBVA',
            'type' => 'card_cutoff',
            'account_id' => $accounts['credit']->id,
            'estimated_amount' => null,
            'frequency' => 'monthly',
            'day_of_month' => 15,
            'advance_days' => 5,
            'is_active' => true,
            'next_date' => now()->addMonth()->day(15),
        ]);
    }
}
