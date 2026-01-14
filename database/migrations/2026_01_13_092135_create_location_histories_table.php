<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('location_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('mould_id');
            $table->uuid('plant_id')->nullable();   // kalau di toolroom/warehouse tetap bisa set plant
            $table->uuid('machine_id')->nullable(); // kalau lokasi MACHINE

            $table->enum('location', ['TOOL_ROOM','WAREHOUSE','IN_TRANSIT','MACHINE']);
            $table->dateTime('start_ts');
            $table->dateTime('end_ts')->nullable(); // null = current

            $table->string('moved_by', 100)->nullable();
            $table->string('note')->nullable();

            $table->timestamps();

            $table->foreign('mould_id')->references('id')->on('moulds')->cascadeOnDelete();
            $table->foreign('plant_id')->references('id')->on('plants')->nullOnDelete();
            $table->foreign('machine_id')->references('id')->on('machines')->nullOnDelete();

            $table->index(['mould_id', 'end_ts']); // cari lokasi current end_ts null
            $table->index(['plant_id']);
            $table->index(['machine_id']);
            $table->index(['location']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('location_histories');
    }
};
