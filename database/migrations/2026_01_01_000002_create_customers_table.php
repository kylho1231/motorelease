<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->string('customer_code', 30)->unique();
            $table->string('first_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100);
            $table->string('suffix', 20)->nullable();
            $table->string('mobile_number', 25);
            $table->string('email', 100)->nullable();
            $table->string('address_line', 255);
            $table->string('barangay', 100);
            $table->string('municipality', 100)->default('San Jose de Buenavista');
            $table->string('province', 100)->default('Antique');
            $table->string('postal_code', 10)->default('5700');
            $table->enum('notification_preference', ['sms', 'email', 'both', 'none'])->default('sms');
            $table->boolean('is_verified')->default(true);
            $table->boolean('privacy_consent')->default(true);
            $table->text('remarks')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['last_name', 'first_name']);
            $table->index('mobile_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
