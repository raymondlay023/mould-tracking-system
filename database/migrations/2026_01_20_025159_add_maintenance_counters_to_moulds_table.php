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
        Schema::table('moulds', function (Blueprint $table) {
            $table->unsignedBigInteger('total_shots')->default(0)->after('status');
            $table->unsignedBigInteger('last_pm_at_shot')->default(0)->after('total_shots');
            $table->timestamp('last_pm_at_ts')->nullable()->after('last_pm_at_shot');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('moulds', function (Blueprint $table) {
            $table->dropColumn(['total_shots', 'last_pm_at_shot', 'last_pm_at_ts']);
        });
    }
};
