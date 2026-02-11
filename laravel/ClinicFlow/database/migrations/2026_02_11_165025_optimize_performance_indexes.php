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
        Schema::table('appointments', function (Blueprint $table) {
            $table->index(['clinic_id', 'status']);
            $table->index(['clinic_id', 'appointment_date', 'appointment_time'], 'idx_clinic_app_time');
            $table->index(['whatsapp_reminder_sent', 'status']);
            $table->index(['sms_reminder_sent', 'status']);
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->index(['clinic_id', 'phone']);
            $table->index('name');
        });

        Schema::table('whatsapp_logs', function (Blueprint $table) {
            $table->index(['clinic_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
