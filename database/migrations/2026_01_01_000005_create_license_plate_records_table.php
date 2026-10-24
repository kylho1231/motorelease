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
        Schema::create('license_plate_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('motorcycle_id')->unique()->constrained('motorcycles')->cascadeOnDelete();
            $table->string('plate_number', 30)->nullable();
            $table->string('mv_file_number', 50)->nullable();
            $table->boolean('is_plate_received')->default(false);
            $table->date('received_date')->nullable();
            $table->foreignId('received_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('verified_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->enum('status', [
                'not_yet_confirmed',
                'awaiting_physical_plate',
                'received_for_verification',
                'ready_for_pickup',
                'on_hold',
                'claimed'
            ])->default('not_yet_confirmed');
            $table->string('storage_bin_ref', 100)->nullable();
            $table->text('hold_reason')->nullable();
            $table->timestamp('claim_date')->nullable();
            $table->foreignId('released_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('plate_number');
            $table->index('verified_at');
            $table->index('claim_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('license_plate_records');
    }
};
