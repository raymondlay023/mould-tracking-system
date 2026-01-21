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
        Schema::table('maintenance_events', function (Blueprint $table) {
            $table->dateTime('end_ts')->nullable()->change();
            $table->string('status', 20)->default('COMPLETED')->after('type'); // REQUESTED, APPROVED, IN_PROGRESS, COMPLETED
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenance_events', function (Blueprint $table) {
            // Cannot easily revert nullable change if nulls exist
            // $table->dateTime('end_ts')->nullable(false)->change(); 
            $table->dropColumn('status');
        });
    }
};
