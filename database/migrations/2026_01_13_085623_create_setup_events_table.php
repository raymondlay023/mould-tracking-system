<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('setup_events', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('mould_id');
            $table->uuid('machine_id');

            $table->dateTime('start_ts');  // atau dateTimeTz kalau mau simpan timezone
            $table->dateTime('end_ts');

            $table->unsignedInteger('target_min')->nullable();
            $table->unsignedInteger('actual_min')->nullable();
            $table->string('loss_reason')->nullable();
            $table->string('operator_name', 100)->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->foreign('mould_id')->references('id')->on('moulds')->cascadeOnDelete();
            $table->foreign('machine_id')->references('id')->on('machines')->cascadeOnDelete();

            $table->index(['mould_id', 'end_ts']);
            $table->index(['machine_id', 'end_ts']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setup_events');
    }
};
