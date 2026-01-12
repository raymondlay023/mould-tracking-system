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
        Schema::create('moulds', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique(); // mould_code unik fisik/digital
            $table->string('name');
            $table->unsignedSmallInteger('cavities'); // >0

            $table->string('customer')->nullable();
            $table->string('resin')->nullable();

            $table->unsignedInteger('min_tonnage_t')->nullable();
            $table->unsignedInteger('max_tonnage_t')->nullable();

            // PM interval (MVP)
            $table->unsignedInteger('pm_interval_shot')->nullable(); // threshold shot
            $table->unsignedInteger('pm_interval_days')->nullable(); // threshold hari

            $table->date('commissioned_at')->nullable();

            // RMP
            $table->timestamp('rmp_last_at')->nullable();
            $table->string('rmp_approved_by')->nullable();

            // status (string biar fleksibel)
            $table->string('status')->default('AVAILABLE');
            // AVAILABLE, IN_SETUP, IN_RUN, IN_MAINTENANCE, IN_TRANSIT

            $table->timestamps();

            $table->index(['status']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moulds');
    }
};
