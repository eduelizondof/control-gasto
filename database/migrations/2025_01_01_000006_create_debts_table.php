<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('debts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name', 150);
            $table->enum('type', [
                'revolving_credit', 'no_interest_installments',
                'personal_loan', 'mortgage', 'auto_loan', 'other'
            ]);
            $table->decimal('total_amount', 14, 2);
            $table->decimal('paid_amount', 14, 2)->default(0);
            $table->decimal('outstanding_balance', 14, 2);
            $table->decimal('interest_rate', 5, 2)->default(0);
            $table->integer('total_payments')->nullable();
            $table->integer('payments_made')->default(0);
            $table->decimal('payment_amount', 14, 2)->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('next_payment_date')->nullable();
            $table->tinyInteger('cutoff_day')->nullable();
            $table->tinyInteger('payment_day')->nullable();
            $table->enum('status', ['active', 'paid_off', 'paused', 'overdue'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('debts');
    }
};
