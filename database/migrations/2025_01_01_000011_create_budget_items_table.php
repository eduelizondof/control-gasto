<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budget_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monthly_budget_id')->constrained()->cascadeOnDelete();
            $table->foreignId('concept_id')->nullable()->constrained()->nullOnDelete();
            $table->string('custom_name', 150)->nullable();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->decimal('estimated_amount', 14, 2);
            $table->enum('frequency', [
                'monthly', 'bimonthly', 'quarterly', 'semiannual', 'annual'
            ])->default('monthly');
            $table->decimal('monthly_amount', 14, 2);
            $table->foreignId('account_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_fixed')->default(true);
            $table->text('notes')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_items');
    }
};
