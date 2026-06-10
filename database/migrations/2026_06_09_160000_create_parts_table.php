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
        Schema::create('parts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('mould_id');
            $table->string('part_number')->unique();
            $table->string('part_name');
            $table->unsignedInteger('cavity_number')->nullable();
            $table->timestamps();

            $table->foreign('mould_id')->references('id')->on('moulds')->cascadeOnDelete();
            $table->index(['mould_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parts');
    }
};
