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
        Schema::create('release_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code', 40)->unique();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('processed_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('claimant_type', ['owner', 'authorized_representative'])->default('owner');
            $table->string('claimant_name', 150);
            $table->string('claimant_contact', 30);
            $table->string('id_type_presented', 80);
            $table->string('id_number_masked', 80);
            $table->boolean('authorization_letter_checked')->default(false);
            $table->timestamp('released_at');
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index('transaction_code');
            $table->index('released_at');
        });

        Schema::create('release_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('release_transaction_id')->constrained('release_transactions')->cascadeOnDelete();
            $table->foreignId('motorcycle_id')->constrained('motorcycles')->cascadeOnDelete();
            $table->enum('item_type', ['orcr', 'license_plate']);
            $table->unsignedBigInteger('item_record_id');
            $table->string('item_identifier', 100)->nullable();
            $table->string('status_before', 50)->default('ready_for_pickup');
            $table->string('status_after', 50)->default('claimed');
            $table->timestamps();

            $table->index(['item_type', 'item_record_id']);
            $table->index('motorcycle_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('release_items');
        Schema::dropIfExists('release_transactions');
    }
};
