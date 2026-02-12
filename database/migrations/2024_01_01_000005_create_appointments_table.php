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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->onDelete('cascade');
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained()->onDelete('cascade');
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->enum('status', ['booked', 'confirmed', 'cancelled', 'completed', 'no_show'])->default('booked');
            $table->text('notes')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->boolean('whatsapp_reminder_sent')->default(false);
            $table->boolean('sms_reminder_sent')->default(false);
            $table->timestamp('whatsapp_reminder_sent_at')->nullable();
            $table->timestamp('sms_reminder_sent_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('clinic_id');
            $table->index('appointment_date');
            $table->index(['doctor_id', 'appointment_date']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
