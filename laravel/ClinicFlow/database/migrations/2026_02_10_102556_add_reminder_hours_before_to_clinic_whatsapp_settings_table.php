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
        Schema::table('clinic_whatsapp_settings', function (Blueprint $table) {
            $table->integer('reminder_hours_before')->default(24)->after('default_template');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clinic_whatsapp_settings', function (Blueprint $table) {
            $table->dropColumn('reminder_hours_before');
        });
    }
};
