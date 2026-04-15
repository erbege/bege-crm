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
        Schema::table('hotspot_profiles', function (Blueprint $table) {
            $table->string('mikrotik_group')->default('RLRADIUS')->after('name');
            $table->string('address_list')->nullable()->after('mikrotik_group');

            $table->bigInteger('data_limit')->nullable()->after('rate_limit')->comment('Quota value');
            $table->string('data_limit_unit')->default('UNLIMITED')->after('data_limit')->comment('MB, GB, UNLIMITED');

            $table->integer('time_limit')->nullable()->after('data_limit_unit')->comment('Duration value');
            $table->string('time_limit_unit')->default('UNLIMITED')->after('time_limit')->comment('minutes, hours, days, UNLIMITED');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hotspot_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'mikrotik_group',
                'address_list',
                'data_limit',
                'data_limit_unit',
                'time_limit',
                'time_limit_unit',
            ]);
        });
    }
};
