<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('time')->nullable();
            $table->foreignId('concept_id')->nullable()->constrained()->nullOnDelete();
            $table->string('description', 255)->nullable();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['income', 'expense', 'transfer', 'savings', 'adjustment']);
            $table->foreignId('source_account_id')->constrained('accounts')->cascadeOnDelete();
            $table->foreignId('destination_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->decimal('amount', 14, 2);
            $table->char('currency', 3)->default('MXN');
            $table->enum('status', ['confirmed', 'pending', 'cancelled'])->default('confirmed');
            $table->text('notes')->nullable();
            $table->string('receipt_url')->nullable();
            $table->foreignId('debt_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('reminder_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('source', ['manual', 'chatbot', 'import', 'automatic'])->default('manual');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['group_id', 'date']);
            $table->index(['group_id', 'type']);
            $table->index(['group_id', 'category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
