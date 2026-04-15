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
        // 1. Add new columns to bw_profiles
        Schema::table('bw_profiles', function (Blueprint $table) {
            $table->string('mikrotik_group')->nullable()->after('rate_limit');
            $table->string('radius_group')->nullable()->after('mikrotik_group');
        });

        // 2. Data Migration: Copy data from packages to bw_profiles
        $bwProfiles = DB::table('bw_profiles')->get();
        foreach ($bwProfiles as $profile) {
            // Get the first package associated with this profile
            $package = DB::table('packages')->where('bw_profile_id', $profile->id)->first();

            $data = [];
            if ($package) {
                $data['mikrotik_group'] = $package->mikrotik_group;
                $data['radius_group'] = $package->radius_group;
            }

            // Fallback for radius_group: use olt_profile_name if radius_group is still empty
            if (empty($data['radius_group']) && !empty($profile->olt_profile_name)) {
                $data['radius_group'] = $profile->olt_profile_name;
            }

            if (!empty($data)) {
                DB::table('bw_profiles')->where('id', $profile->id)->update($data);
            }
        }

        // 3. Cleanup: Drop old columns
        Schema::table('bw_profiles', function (Blueprint $table) {
            $table->dropColumn('olt_profile_name');
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['mikrotik_group', 'radius_group', 'olt_profile_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Restore columns to packages
        Schema::table('packages', function (Blueprint $table) {
            $table->string('mikrotik_group')->nullable()->after('is_active');
            $table->string('radius_group')->nullable()->after('mikrotik_group');
            $table->string('olt_profile_name')->nullable()->after('radius_group');
        });

        // 2. Restore olt_profile_name to bw_profiles
        Schema::table('bw_profiles', function (Blueprint $table) {
            $table->string('olt_profile_name')->nullable()->after('radius_group');
        });

        // 3. Data Migration: Reverse (Copy from bw_profiles back to packages)
        $bwProfiles = DB::table('bw_profiles')->get();
        foreach ($bwProfiles as $profile) {
            DB::table('packages')->where('bw_profile_id', $profile->id)->update([
                'mikrotik_group' => $profile->mikrotik_group,
                'radius_group' => $profile->radius_group,
                'olt_profile_name' => $profile->radius_group, // Best guess
            ]);

            DB::table('bw_profiles')->where('id', $profile->id)->update([
                'olt_profile_name' => $profile->radius_group,
            ]);
        }

        // 4. Drop new columns from bw_profiles
        Schema::table('bw_profiles', function (Blueprint $table) {
            $table->dropColumn(['mikrotik_group', 'radius_group']);
        });
    }
};
