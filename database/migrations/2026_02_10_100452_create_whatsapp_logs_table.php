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
        Schema::create('whatsapp_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->onDelete('cascade');
            $table->foreignId('appointment_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('direction', ['incoming', 'outgoing']);
            $table->string('phone');
            $table->string('template_name')->nullable();
            $table->json('payload')->nullable();
            $table->json('response')->nullable();
            $table->enum('status', ['pending', 'sent', 'failed', 'received'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['clinic_id', 'direction', 'created_at']);
            $table->index('appointment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_logs');
    }
};
