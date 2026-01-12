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
        Schema::create('machines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('plant_id');
            $table->uuid('zone_id')->nullable();
            $table->string('code')->unique();     // ID unik mesin
            $table->string('name');
            $table->unsignedInteger('tonnage_t')->nullable();
            $table->boolean('plc_connected')->default(false);
            $table->timestamps();

            $table->foreign('plant_id')->references('id')->on('plants')->cascadeOnDelete();
            $table->foreign('zone_id')->references('id')->on('zones')->nullOnDelete();
            $table->index(['plant_id', 'zone_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machines');
    }
};
