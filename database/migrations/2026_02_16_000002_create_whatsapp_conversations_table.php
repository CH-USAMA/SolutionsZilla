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
        Schema::create('whatsapp_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->onDelete('cascade');
            $table->string('conversation_id')->unique(); // Meta Conversation ID
            $table->string('phone_number', 20)->index();
            $table->timestamp('started_at');
            $table->timestamp('expires_at')->nullable(); // 24h window
            $table->timestamp('last_message_at')->nullable();
            $table->string('type')->nullable(); // marketing, utility, authentication, service
            $table->string('category')->nullable(); // user_initiated, business_initiated
            $table->integer('message_count')->default(0);
            $table->boolean('is_billable')->default(true);
            $table->decimal('cost', 10, 4)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->timestamps();

            $table->index(['clinic_id', 'started_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_conversations');
    }
};
