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
        Schema::create('billing_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['setup', 'monthly', 'other'])->default('monthly');
            $table->decimal('amount', 10, 2);
            $table->date('billing_date');
            $table->date('due_date');
            $table->date('paid_date')->nullable();
            $table->enum('status', ['paid', 'unpaid', 'overdue'])->default('unpaid');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('clinic_id');
            $table->index('billing_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_records');
    }
};
