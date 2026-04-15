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
        Schema::create('invoice_payment_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->enum('action', ['payment', 'rollback', 'cancelled']); // payment = pembayaran, rollback = pembatalan pembayaran
            $table->decimal('amount', 15, 2);
            $table->string('payment_method')->nullable(); // cash, transfer, e-wallet, qris
            $table->string('reference')->nullable(); // nomor referensi/bukti transfer
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['invoice_id', 'action']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_payment_histories');
    }
};
