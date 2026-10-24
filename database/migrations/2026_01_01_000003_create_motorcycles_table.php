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
        Schema::create('motorcycles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->string('tracking_reference', 40)->unique();
            $table->string('tracking_pin_hash', 255)->nullable();
            $table->string('manufacturer', 60)->default('Yamaha');
            $table->string('model', 100);
            $table->string('variant', 80)->nullable();
            $table->string('color', 60);
            $table->unsignedSmallInteger('year_model')->default(2026);
            $table->string('engine_number', 80)->unique();
            $table->string('chassis_number', 80)->unique();
            $table->string('dealer_transaction_ref', 80)->nullable();
            $table->string('registration_reference', 80)->nullable();
            $table->date('purchase_date');
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('tracking_reference');
            $table->index('engine_number');
            $table->index('chassis_number');
            $table->index('purchase_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motorcycles');
    }
};
