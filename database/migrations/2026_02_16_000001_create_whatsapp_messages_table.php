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
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->onDelete('cascade');
            $table->string('message_id')->unique(); // Meta Message ID
            $table->string('wamid')->nullable()->index(); // Meta WAMID (sometimes different)
            $table->string('from', 20)->index();
            $table->string('to', 20)->index();
            $table->string('type', 20)->default('text'); // text, template, image, etc.
            $table->enum('direction', ['incoming', 'outgoing']);
            $table->text('body')->nullable();
            $table->string('status', 20)->default('pending'); // pending, sent, delivered, read, failed
            $table->json('metadata')->nullable(); // Store raw payload or extra data
            $table->string('conversation_id')->nullable()->index(); // Link to conversation
            $table->timestamps();
            $table->softDeletes();

            $table->index(['clinic_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
    }
};
