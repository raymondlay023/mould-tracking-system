<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Convert all datetime/timestamp columns to timestampTz for proper UTC storage
     * with timezone information.
     */
    public function up(): void
    {
        // Convert maintenance_events table
        Schema::table('maintenance_events', function (Blueprint $table) {
            $table->timestampTz('start_ts')->change();
            $table->timestampTz('end_ts')->nullable()->change();
        });

        // Convert setup_events table
        Schema::table('setup_events', function (Blueprint $table) {
            $table->timestampTz('start_ts')->change();
            $table->timestampTz('end_ts')->nullable()->change();
        });

        // Convert trial_events table
        Schema::table('trial_events', function (Blueprint $table) {
            $table->timestampTz('start_ts')->change();
            $table->timestampTz('end_ts')->nullable()->change();
            $table->timestampTz('approved_at')->nullable()->change();
        });

        // Convert location_histories table
        Schema::table('location_histories', function (Blueprint $table) {
            $table->timestampTz('start_ts')->change();
            $table->timestampTz('end_ts')->nullable()->change();
        });

        // Convert moulds table timestamps
        Schema::table('moulds', function (Blueprint $table) {
            $table->timestampTz('rmp_last_at')->nullable()->change();
            $table->timestampTz('last_pm_at_ts')->nullable()->change();
        });

        // Convert notifications table
        Schema::table('notifications', function (Blueprint $table) {
            $table->timestampTz('read_at')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * Convert timestampTz back to datetime for rollback compatibility.
     */
    public function down(): void
    {
        // Convert maintenance_events table back
        Schema::table('maintenance_events', function (Blueprint $table) {
            $table->dateTime('start_ts')->change();
            $table->dateTime('end_ts')->nullable()->change();
        });

        // Convert setup_events table back
        Schema::table('setup_events', function (Blueprint $table) {
            $table->dateTime('start_ts')->change();
            $table->dateTime('end_ts')->nullable()->change();
        });

        // Convert trial_events table back
        Schema::table('trial_events', function (Blueprint $table) {
            $table->dateTime('start_ts')->change();
            $table->dateTime('end_ts')->nullable()->change();
            $table->dateTime('approved_at')->nullable()->change();
        });

        // Convert location_histories table back
        Schema::table('location_histories', function (Blueprint $table) {
            $table->dateTime('start_ts')->change();
            $table->dateTime('end_ts')->nullable()->change();
        });

        // Convert moulds table timestamps back
        Schema::table('moulds', function (Blueprint $table) {
            $table->timestamp('rmp_last_at')->nullable()->change();
            $table->timestamp('last_pm_at_ts')->nullable()->change();
        });

        // Convert notifications table back
        Schema::table('notifications', function (Blueprint $table) {
            $table->timestamp('read_at')->nullable()->change();
        });
    }
};
