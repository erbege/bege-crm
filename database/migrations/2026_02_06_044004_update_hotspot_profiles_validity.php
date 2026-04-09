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
            $table->integer('validity_value')->after('price')->default(0);
            $table->enum('validity_unit', ['hours', 'days', 'weeks', 'months'])->after('validity_value')->default('days');
        });

        // Migrate existing data
        DB::table('hotspot_profiles')->get()->each(function ($profile) {
            DB::table('hotspot_profiles')
                ->where('id', $profile->id)
                ->update([
                    'validity_value' => $profile->validity_days,
                    'validity_unit' => 'days',
                ]);
        });

        Schema::table('hotspot_profiles', function (Blueprint $table) {
            $table->dropColumn('validity_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hotspot_profiles', function (Blueprint $table) {
            $table->integer('validity_days')->after('price')->default(1);
        });

        // Restore data (assuming days)
        DB::table('hotspot_profiles')->get()->each(function ($profile) {
            $days = $profile->validity_value;
            // Simple conversion back to days if possible, or just keep value
            if ($profile->validity_unit == 'weeks')
                $days *= 7;
            if ($profile->validity_unit == 'months')
                $days *= 30;
            if ($profile->validity_unit == 'hours')
                $days = ceil($days / 24);

            DB::table('hotspot_profiles')
                ->where('id', $profile->id)
                ->update(['validity_days' => $days]);
        });

        Schema::table('hotspot_profiles', function (Blueprint $table) {
            $table->dropColumn(['validity_value', 'validity_unit']);
        });
    }
};
