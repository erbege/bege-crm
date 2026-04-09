<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * 
     * Memindahkan field subscription dari customers ke subscriptions table:
     * - package_id
     * - installation_date
     * - status
     * - pppoe_username
     * - pppoe_password
     * - device_sn
     */
    public function up(): void
    {
        // 1. Tambahkan field teknis ke subscriptions
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->date('installation_date')->nullable()->after('period_end');
            $table->string('pppoe_username')->nullable()->after('notes');
            $table->string('pppoe_password')->nullable()->after('pppoe_username');
            $table->string('device_sn')->nullable()->after('pppoe_password');
        });

        // 2. Hapus field dari customers (yang sudah dipindahkan ke subscriptions)
        Schema::table('customers', function (Blueprint $table) {
            // Drop foreign key terlebih dahulu
            $table->dropForeign(['package_id']);
            $table->dropColumn([
                'package_id',
                'installation_date',
                'status',
                'pppoe_username',
                'pppoe_password',
                'device_sn',
            ]);
        });

        // 3. Tambahkan registered_at di customers sebagai pengganti installation_date
        Schema::table('customers', function (Blueprint $table) {
            $table->date('registered_at')->nullable()->after('coverage_point_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan field ke customers
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('registered_at');

            $table->foreignId('package_id')->nullable()->constrained('packages')->onDelete('restrict');
            $table->date('installation_date')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended', 'isolated'])->default('active');
            $table->string('pppoe_username')->nullable();
            $table->string('pppoe_password')->nullable();
            $table->string('device_sn')->nullable();
        });

        // Hapus field dari subscriptions
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn([
                'installation_date',
                'pppoe_username',
                'pppoe_password',
                'device_sn',
            ]);
        });
    }
};
