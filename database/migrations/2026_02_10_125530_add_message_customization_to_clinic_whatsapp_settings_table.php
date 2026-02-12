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
            $table->string('message_type')->default('template')->after('default_template'); // template or text
            $table->text('custom_message')->nullable()->after('message_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clinic_whatsapp_settings', function (Blueprint $table) {
            $table->dropColumn(['message_type', 'custom_message']);
        });
    }
};
