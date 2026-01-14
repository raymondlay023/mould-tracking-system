<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('maintenance_events', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('mould_id');
            $table->dateTime('start_ts');
            $table->dateTime('end_ts');

            $table->enum('type', ['PM','CM']); // preventive / corrective
            $table->string('description')->nullable();
            $table->text('parts_used')->nullable();

            $table->unsignedInteger('downtime_min')->default(0);
            $table->unsignedInteger('cost')->nullable(); // optional MVP

            // next due rules
            $table->unsignedInteger('next_due_shot')->nullable();
            $table->date('next_due_date')->nullable();

            $table->string('performed_by', 100)->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->foreign('mould_id')->references('id')->on('moulds')->cascadeOnDelete();

            $table->index(['mould_id', 'end_ts']);
            $table->index(['type', 'end_ts']);
            $table->index(['next_due_date']);
            $table->index(['next_due_shot']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_events');
    }
};
