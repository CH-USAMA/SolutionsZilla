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
        // Modify users table
        Schema::table('users', function (Blueprint $table) {
            // Change role to string to accept 'super_admin', 'doctor', etc.
            // Make clinic_id nullable for Super Admin who doesn't belong to a specific clinic
            $table->string('role', 50)->default('receptionist')->change();
            $table->unsignedBigInteger('clinic_id')->nullable()->change();
        });

        // Add user_id to doctors table
        Schema::table('doctors', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        // Revert users table changes is tricky with raw SQL without losing data if we truncate to enum
        // We will just leave them as is or try to revert if sure
        // For now, we'll just revert clinic_id to not null if we could, but data might violate it.
        // So we will just leave it.
    }
};
