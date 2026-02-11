<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('whatsapp_logs', function (Blueprint $table) {
            $table->index('phone');
            $table->index(['status', 'direction']);
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->index(['status', 'appointment_date', 'appointment_time'], 'idx_appointments_lookup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('whatsapp_logs', function (Blueprint $table) {
            $table->dropIndex(['phone']);
            $table->dropIndex(['status', 'direction']);
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->dropIndex('idx_appointments_lookup');
        });
    }
};
