<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * 
     * Memindahkan coverage_point_id dari customers ke subscriptions
     */
    public function up(): void
    {
        // 1. Tambahkan coverage_point_id ke subscriptions
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->foreignId('coverage_point_id')
                ->nullable()
                ->after('package_id')
                ->constrained('coverage_points')
                ->nullOnDelete();
        });

        // 2. Hapus coverage_point_id dari customers
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['coverage_point_id']);
            $table->dropColumn('coverage_point_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan coverage_point_id ke customers
        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('coverage_point_id')
                ->nullable()
                ->constrained('coverage_points')
                ->nullOnDelete();
        });

        // Hapus dari subscriptions
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign(['coverage_point_id']);
            $table->dropColumn('coverage_point_id');
        });
    }
};
