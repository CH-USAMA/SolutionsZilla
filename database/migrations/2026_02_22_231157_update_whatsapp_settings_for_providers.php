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
            $table->string('provider')->default('meta')->after('clinic_id');
            $table->string('js_api_url')->nullable()->after('custom_message');
            $table->string('js_api_key')->nullable()->after('js_api_url');
            $table->string('js_session_id')->nullable()->after('js_api_key');
            $table->string('js_connection_status')->default('disconnected')->after('js_session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clinic_whatsapp_settings', function (Blueprint $table) {
            $table->dropColumn(['provider', 'js_api_url', 'js_api_key', 'js_session_id', 'js_connection_status']);
        });
    }
};
