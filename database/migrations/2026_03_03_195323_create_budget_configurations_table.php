<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('budget_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->cascadeOnDelete();
            $table->decimal('necessities_percentage', 5, 2)->default(50.00);
            $table->decimal('debts_percentage', 5, 2)->default(25.00);
            $table->decimal('future_percentage', 5, 2)->default(15.00);
            $table->decimal('desires_percentage', 5, 2)->default(10.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_configurations');
    }
};
