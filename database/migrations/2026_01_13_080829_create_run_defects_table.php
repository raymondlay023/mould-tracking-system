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
        Schema::create('run_defects', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('run_id');
            $table->string('defect_code', 50);
            $table->unsignedInteger('qty')->default(0);

            $table->timestamps();

            $table->foreign('run_id')->references('id')->on('production_runs')->cascadeOnDelete();

            $table->index(['run_id']);
            $table->index(['defect_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('run_defects');
    }
};
