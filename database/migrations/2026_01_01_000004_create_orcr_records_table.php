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
        Schema::create('orcr_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('motorcycle_id')->unique()->constrained('motorcycles')->cascadeOnDelete();
            $table->string('or_number', 50)->nullable();
            $table->string('cr_number', 50)->nullable();
            $table->boolean('is_or_received')->default(false);
            $table->date('or_received_date')->nullable();
            $table->boolean('is_cr_received')->default(false);
            $table->date('cr_received_date')->nullable();
            $table->foreignId('received_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('verified_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->enum('status', [
                'not_yet_confirmed',
                'awaiting_documents',
                'partially_received',
                'ready_for_pickup',
                'on_hold',
                'claimed'
            ])->default('not_yet_confirmed');
            $table->string('storage_location_ref', 100)->nullable();
            $table->text('hold_reason')->nullable();
            $table->timestamp('claim_date')->nullable();
            $table->foreignId('released_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('verified_at');
            $table->index('claim_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orcr_records');
    }
};
