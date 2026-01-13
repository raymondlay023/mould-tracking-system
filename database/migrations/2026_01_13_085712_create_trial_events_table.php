<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trial_events', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('mould_id');
            $table->uuid('machine_id');

            $table->dateTime('start_ts');
            $table->dateTime('end_ts');

            $table->string('purpose')->nullable();
            $table->text('parameters')->nullable(); // ringkas parameter kunci (optional)
            $table->text('notes')->nullable();

            // approval
            $table->boolean('approved')->default(false);
            $table->boolean('approved_go')->nullable(); // true=Go, false=No-Go, null=belum
            $table->string('approved_by', 100)->nullable();
            $table->dateTime('approved_at')->nullable();

            $table->timestamps();

            $table->foreign('mould_id')->references('id')->on('moulds')->cascadeOnDelete();
            $table->foreign('machine_id')->references('id')->on('machines')->cascadeOnDelete();

            $table->index(['mould_id', 'end_ts']);
            $table->index(['approved', 'approved_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trial_events');
    }
};
