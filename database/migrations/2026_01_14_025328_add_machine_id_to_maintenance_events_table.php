<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenance_events', function (Blueprint $table) {
            $table->uuid('machine_id')->nullable()->after('mould_id');
            $table->uuid('plant_id')->nullable()->after('machine_id'); // optional tapi bagus buat filter cepat

            $table->foreign('machine_id')->references('id')->on('machines')->nullOnDelete();
            $table->foreign('plant_id')->references('id')->on('plants')->nullOnDelete();

            $table->index(['machine_id', 'end_ts']);
            $table->index(['plant_id', 'end_ts']);
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_events', function (Blueprint $table) {
            $table->dropForeign(['machine_id']);
            $table->dropForeign(['plant_id']);
            $table->dropIndex(['machine_id', 'end_ts']);
            $table->dropIndex(['plant_id', 'end_ts']);
            $table->dropColumn(['machine_id', 'plant_id']);
        });
    }
};
