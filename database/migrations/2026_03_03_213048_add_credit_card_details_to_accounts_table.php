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
        Schema::table('accounts', function (Blueprint $table) {
            $table->decimal('annual_fee', 10, 2)->nullable()->after('payment_day');
            $table->integer('annual_fee_month')->nullable()->after('annual_fee');
            $table->text('notes')->nullable()->after('annual_fee_month');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn(['annual_fee', 'annual_fee_month', 'notes']);
        });
    }
};
