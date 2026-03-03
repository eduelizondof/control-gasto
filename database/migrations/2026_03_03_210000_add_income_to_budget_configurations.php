<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('budget_configurations', function (Blueprint $table) {
            $table->decimal('fixed_monthly_income', 14, 2)->default(0)->after('group_id');
            $table->decimal('total_monthly_income', 14, 2)->default(0)->after('fixed_monthly_income');
        });
    }

    public function down(): void
    {
        Schema::table('budget_configurations', function (Blueprint $table) {
            $table->dropColumn(['fixed_monthly_income', 'total_monthly_income']);
        });
    }
};
