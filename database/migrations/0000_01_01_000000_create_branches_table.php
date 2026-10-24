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
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name', 150);
            $table->string('dealership_brand', 100)->default('Yamaha 3S Authorized Dealership');
            $table->string('address_line', 255);
            $table->string('municipality', 100)->default('San Jose de Buenavista');
            $table->string('province', 100)->default('Antique');
            $table->string('postal_code', 10)->default('5700');
            $table->string('contact_number', 50);
            $table->string('email', 100)->nullable();
            $table->string('operating_hours', 150)->default('Mon - Sat: 8:00 AM - 5:00 PM');
            $table->text('pickup_instructions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
