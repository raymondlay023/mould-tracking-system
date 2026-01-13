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
        Schema::create('production_runs', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('mould_id');
            $table->uuid('machine_id');

            $table->timestampTz('start_ts');
            $table->timestampTz('end_ts')->nullable(); // null = active

            // snapshot cavities saat start run (biar historinya stabil)
            $table->unsignedInteger('cavities_snapshot');

            // diisi saat close
            $table->unsignedInteger('shot_total')->default(0);
            $table->unsignedInteger('part_total')->default(0); // shot_total * cavities_snapshot
            $table->unsignedInteger('ok_part')->default(0);
            $table->unsignedInteger('ng_part')->default(0);

            $table->unsignedInteger('cycle_time_avg_sec')->nullable(); // optional MVP
            $table->string('operator_name')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            // FK
            $table->foreign('mould_id')->references('id')->on('moulds')->cascadeOnDelete();
            $table->foreign('machine_id')->references('id')->on('machines')->cascadeOnDelete();

            // Index untuk query cepat
            $table->index(['mould_id', 'end_ts']);
            $table->index(['machine_id', 'end_ts']);

            // Active run query: end_ts is null
            $table->index(['end_ts']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_runs');
    }
};
