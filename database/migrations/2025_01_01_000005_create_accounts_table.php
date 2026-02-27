<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->enum('type', ['cash', 'debit', 'credit', 'investment', 'savings', 'emergency', 'fund']);
            $table->string('bank', 100)->nullable();
            $table->char('currency', 3)->default('MXN');

            // Balances
            $table->decimal('initial_balance', 14, 2)->default(0);
            $table->decimal('current_balance', 14, 2)->default(0);
            $table->decimal('estimated_balance', 14, 2)->default(0);
            $table->decimal('cutoff_balance', 14, 2)->nullable();

            // Credit card fields
            $table->decimal('credit_limit', 14, 2)->nullable();
            $table->tinyInteger('cutoff_day')->nullable();
            $table->tinyInteger('payment_day')->nullable();

            // Display
            $table->char('color', 7)->default('#6366F1');
            $table->string('icon', 50)->default('wallet');
            $table->boolean('include_in_total')->default(true);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
