<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('budget_items', function (Blueprint $table) {
            $table->tinyInteger('payment_month')->nullable()->after('is_active');
            $table->tinyInteger('payment_day')->nullable()->after('payment_month');
        });
    }

    public function down(): void
    {
        Schema::table('budget_items', function (Blueprint $table) {
            $table->dropColumn(['payment_month', 'payment_day']);
        });
    }
};
