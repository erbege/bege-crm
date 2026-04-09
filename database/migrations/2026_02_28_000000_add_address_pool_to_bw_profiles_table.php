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
        Schema::table('bw_profiles', function (Blueprint $table) {
            $table->string('address_pool')->nullable()->after('olt_profile_name')
                ->comment('Mikrotik IP Pool name for Framed-Pool attribute');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bw_profiles', function (Blueprint $table) {
            $table->dropColumn('address_pool');
        });
    }
};
