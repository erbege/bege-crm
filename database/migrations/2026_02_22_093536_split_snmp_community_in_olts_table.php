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
        Schema::table('olts', function (Blueprint $table) {
            $table->dropColumn('snmp_community');
            $table->string('snmp_community_read')->nullable()->default('public')->after('snmp_version');
            $table->string('snmp_community_write')->nullable()->default('private')->after('snmp_community_read');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('olts', function (Blueprint $table) {
            $table->dropColumn(['snmp_community_read', 'snmp_community_write']);
            $table->string('snmp_community')->nullable()->default('public')->after('snmp_version');
        });
    }
};
