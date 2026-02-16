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
        Schema::create('whatsapp_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->onDelete('cascade');
            $table->integer('month');
            $table->integer('year');
            $table->integer('conversations_count')->default(0);
            $table->integer('messages_sent')->default(0);
            $table->integer('messages_delivered')->default(0);
            $table->decimal('estimated_cost', 10, 2)->default(0.00);
            $table->string('currency', 3)->default('USD');
            $table->json('breakdown')->nullable(); // JSON breakdown by conversation type
            $table->timestamps();

            $table->unique(['clinic_id', 'month', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_usage');
    }
};
