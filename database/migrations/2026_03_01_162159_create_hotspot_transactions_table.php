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
        Schema::create('hotspot_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->foreignId('hotspot_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hotspot_voucher_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name')->nullable();
            $table->string('customer_contact'); // WhatsApp or Email
            $table->decimal('amount', 12, 2);
            $table->enum('status', ['unpaid', 'paid', 'failed', 'cancelled'])->default('unpaid');
            $table->string('payment_method')->nullable();
            $table->string('external_reference')->nullable();
            $table->datetime('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotspot_transactions');
    }
};
