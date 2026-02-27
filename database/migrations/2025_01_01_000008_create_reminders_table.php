<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->cascadeOnDelete();
            $table->string('name', 150);
            $table->enum('type', [
                'fixed_payment', 'card_cutoff', 'annuity',
                'expiration', 'debt', 'custom'
            ]);
            $table->foreignId('account_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('debt_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('concept_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('estimated_amount', 14, 2)->nullable();
            $table->enum('frequency', [
                'one_time', 'weekly', 'biweekly', 'monthly',
                'bimonthly', 'quarterly', 'semiannual', 'annual'
            ])->default('monthly');
            $table->tinyInteger('day_of_month')->nullable();
            $table->date('specific_date')->nullable();
            $table->integer('advance_days')->default(3);
            $table->boolean('auto_create_transaction')->default(false);
            $table->boolean('is_active')->default(true);
            $table->date('next_date')->nullable();
            $table->timestamp('last_executed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
