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
            if (!Schema::hasColumn('clinic_whatsapp_settings', 'waba_id')) {
                $table->string('waba_id')->nullable()->after('phone_number_id'); // WhatsApp Business Account ID
            }
            if (!Schema::hasColumn('clinic_whatsapp_settings', 'verify_token')) {
                $table->string('verify_token')->nullable()->after('access_token'); // Per-clinic webhook token
            }
            if (!Schema::hasColumn('clinic_whatsapp_settings', 'display_phone_number')) {
                $table->string('display_phone_number')->nullable()->after('phone_number_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clinic_whatsapp_settings', function (Blueprint $table) {
            $table->dropColumn(['waba_id', 'verify_token', 'display_phone_number']);
        });
    }
};
