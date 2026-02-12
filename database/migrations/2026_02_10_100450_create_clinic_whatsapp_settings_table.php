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
        Schema::create('clinic_whatsapp_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->onDelete('cascade');
            $table->string('phone_number_id'); // Meta WhatsApp phone number ID
            $table->text('access_token'); // Will be encrypted via model
            $table->string('default_template')->default('appointment_reminder');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique('clinic_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinic_whatsapp_settings');
    }
};
