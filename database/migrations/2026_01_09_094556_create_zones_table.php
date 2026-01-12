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
        Schema::create('zones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('plant_id');
            $table->string('code'); // contoh: Z1, Z2
            $table->string('name');
            $table->timestamps();

            $table->foreign('plant_id')->references('id')->on('plants')->cascadeOnDelete();
            $table->unique(['plant_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zones');
    }
};
