<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('period_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->nullable()->constrained()->nullOnDelete();
            $table->char('period', 7); // '2025-03'
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('starting_balance', 14, 2)->default(0);
            $table->decimal('ending_balance', 14, 2)->default(0);
            $table->decimal('total_income', 14, 2)->default(0);
            $table->decimal('total_expenses', 14, 2)->default(0);
            $table->decimal('total_savings', 14, 2)->default(0);
            $table->decimal('total_transfers', 14, 2)->default(0);
            $table->decimal('estimated_expenses', 14, 2)->default(0);
            $table->decimal('difference', 14, 2)->default(0);
            $table->boolean('is_closed')->default(false);
            $table->timestamps();

            $table->unique(['group_id', 'account_id', 'period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('period_snapshots');
    }
};
