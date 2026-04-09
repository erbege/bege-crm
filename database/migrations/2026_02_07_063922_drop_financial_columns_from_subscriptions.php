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
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn([
                'amount',
                'discount',
                'tax',
                'total',
                'paid_at',
                'payment_method',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->decimal('amount', 12, 2)->default(0)->after('installation_date');
            $table->decimal('discount', 12, 2)->default(0)->after('amount');
            $table->decimal('tax', 12, 2)->default(0)->after('discount');
            $table->decimal('total', 12, 2)->default(0)->after('tax');
            $table->timestamp('paid_at')->nullable()->after('status');
            $table->string('payment_method')->nullable()->after('paid_at');
        });
    }
};
