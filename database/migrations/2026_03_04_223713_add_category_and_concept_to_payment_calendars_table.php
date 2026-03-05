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
        Schema::table('payment_calendars', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->foreignId('concept_id')->nullable()->constrained('concepts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_calendars', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['concept_id']);
            $table->dropColumn(['category_id', 'concept_id']);
        });
    }
};

