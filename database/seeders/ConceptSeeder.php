<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Concept;
use Illuminate\Database\Seeder;

class ConceptSeeder extends Seeder
{
    /**
     * Standard concepts organized by category name.
     * These are seeded as is_system = true.
     */
    private array $conceptsByCategory = [
        // ── Expense categories ──
        'Vivienda' => [
            ['name' => 'Renta', 'description' => 'Pago mensual de renta'],
            ['name' => 'Mantenimiento hogar', 'description' => 'Reparaciones y mantenimiento del hogar'],
            ['name' => 'Predial', 'description' => 'Impuesto predial'],
        ],
        'Alimentación' => [
            ['name' => 'Supermercado', 'description' => 'Compras en supermercado'],
            ['name' => 'Comida rápida', 'description' => 'Fast food y comida para llevar'],
            ['name' => 'Frutas y verduras', 'description' => 'Mercado de frutas y verduras'],
            ['name' => 'Salidas a comer', 'description' => 'Restaurantes y comidas fuera'],
        ],
        'Transporte' => [
            ['name' => 'Gasolina', 'description' => 'Combustible'],
            ['name' => 'Uber / Taxi', 'description' => 'Servicio de transporte privado'],
            ['name' => 'Estacionamiento', 'description' => 'Pago de estacionamiento'],
            ['name' => 'Peajes', 'description' => 'Casetas y peajes'],
        ],
        'Servicios' => [
            ['name' => 'Luz (CFE)', 'description' => 'Pago de electricidad CFE'],
            ['name' => 'Agua', 'description' => 'Pago de servicio de agua'],
            ['name' => 'Internet', 'description' => 'Servicio de internet'],
            ['name' => 'Teléfono', 'description' => 'Plan de telefonía celular'],
            ['name' => 'Gas', 'description' => 'Servicio de gas'],
            ['name' => 'Netflix', 'description' => 'Suscripción Netflix'],
            ['name' => 'Spotify', 'description' => 'Suscripción Spotify'],
        ],
        'Salud' => [
            ['name' => 'Consulta médica', 'description' => 'Visitas al doctor'],
            ['name' => 'Medicamentos', 'description' => 'Compra de medicinas'],
            ['name' => 'Seguro médico', 'description' => 'Pago de seguro de gastos médicos'],
        ],
        'Educación' => [
            ['name' => 'Colegiatura', 'description' => 'Pago de colegiatura'],
            ['name' => 'Materiales', 'description' => 'Útiles y materiales escolares'],
            ['name' => 'Cursos', 'description' => 'Cursos y capacitaciones'],
        ],
        'Entretenimiento' => [
            ['name' => 'Cine', 'description' => 'Entradas al cine'],
            ['name' => 'Salidas', 'description' => 'Entretenimiento y salidas'],
            ['name' => 'Suscripciones', 'description' => 'Otras suscripciones digitales'],
        ],
        'Ropa' => [
            ['name' => 'Ropa personal', 'description' => 'Compra de ropa'],
            ['name' => 'Calzado', 'description' => 'Compra de calzado'],
        ],
        'Otros gastos' => [
            ['name' => 'Pago Anualidad', 'description' => 'Anualidad de tarjeta de crédito u otros'],
            ['name' => 'Pago Tarjeta', 'description' => 'Pago a tarjeta de crédito'],
            ['name' => 'Regalos', 'description' => 'Compra de regalos'],
            ['name' => 'Imprevistos', 'description' => 'Gastos no planeados'],
        ],

        // ── Income categories ──
        'Salario' => [
            ['name' => 'Quincena 1', 'description' => 'Primera quincena del mes'],
            ['name' => 'Quincena 2', 'description' => 'Segunda quincena del mes'],
            ['name' => 'Aguinaldo', 'description' => 'Pago de aguinaldo'],
            ['name' => 'Bonos', 'description' => 'Bonos y compensaciones extras'],
        ],
        'Freelance' => [
            ['name' => 'Proyecto freelance', 'description' => 'Ingreso por proyecto independiente'],
        ],
        'Otros ingresos' => [
            ['name' => 'Venta de artículos', 'description' => 'Venta de cosas usadas o productos'],
            ['name' => 'Devolución', 'description' => 'Devoluciones o reembolsos'],
        ],

        // ── Savings categories ──
        'Ahorro general' => [
            ['name' => 'Fondo de emergencia', 'description' => 'Ahorro para emergencias'],
            ['name' => 'Ahorro vacaciones', 'description' => 'Ahorro para vacaciones'],
        ],
        'Fondo de emergencia' => [
            ['name' => 'Aportación emergencia', 'description' => 'Aportación al fondo de emergencia'],
        ],
    ];

    public function run(): void
    {
        $this->command->info('Seeding standard concepts...');

        // Get all categories grouped by name
        $categories = Category::all()->keyBy('name');

        $created = 0;

        foreach ($this->conceptsByCategory as $categoryName => $concepts) {
            $category = $categories->get($categoryName);

            if (!$category) {
                $this->command->warn("  Category '{$categoryName}' not found, skipping its concepts.");
                continue;
            }

            foreach ($concepts as $conceptData) {
                Concept::firstOrCreate(
                    [
                        'group_id' => $category->group_id,
                        'category_id' => $category->id,
                        'name' => $conceptData['name'],
                    ],
                    [
                        'description' => $conceptData['description'],
                        'is_system' => true,
                    ]
                );
                $created++;
            }
        }

        $this->command->info("  {$created} concepts seeded successfully.");
    }
}
