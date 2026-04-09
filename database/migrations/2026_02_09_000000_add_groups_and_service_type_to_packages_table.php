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
        Schema::table('packages', function (Blueprint $table) {
            $table->string('mikrotik_group')->nullable()->after('description');
            $table->string('radius_group')->nullable()->after('mikrotik_group');
            $table->enum('service_type', ['PPP', 'DHCP', 'HOTSPOT'])->default('PPP')->after('radius_group');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['mikrotik_group', 'radius_group', 'service_type']);
        });
    }
};
