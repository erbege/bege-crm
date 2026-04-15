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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('package_id')->constrained('packages')->onDelete('restrict');

            // Periode Langganan
            $table->date('period_start');
            $table->date('period_end');

            // Tagihan
            $table->decimal('amount', 15, 2); // Jumlah tagihan
            $table->decimal('discount', 15, 2)->default(0); // Diskon
            $table->decimal('total', 15, 2); // Total setelah diskon

            // Status Pembayaran
            $table->enum('status', ['unpaid', 'paid', 'partial', 'cancelled'])->default('unpaid');
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_method')->nullable(); // transfer, cash, e-wallet, etc

            $table->text('notes')->nullable();
            $table->timestamps();

            // Index untuk query performa
            $table->index(['customer_id', 'period_start']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
