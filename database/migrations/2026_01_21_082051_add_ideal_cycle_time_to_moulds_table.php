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
            $table->float('ideal_cycle_time', 8, 2)->nullable()->after('pm_interval_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('moulds', function (Blueprint $table) {
            $table->dropColumn('ideal_cycle_time');
        });
    }
};
