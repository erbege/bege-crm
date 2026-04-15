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
            $table->string('service_type')->default('ppp')->after('status'); // ppp, dhcp, hotspot
            $table->string('mac_address')->nullable()->after('device_sn');
            $table->string('ip_address')->nullable()->after('mac_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['service_type', 'mac_address', 'ip_address']);
        });
    }
};
