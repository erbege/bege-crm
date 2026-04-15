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
        if (!Schema::hasTable('olts')) {
            Schema::create('olts', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('ip_address');
                $table->integer('port')->default(22); // SSH Port
                $table->string('username')->nullable();
                $table->string('password')->nullable();
                $table->string('brand')->default('zte'); // zte, huawei, etc
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        Schema::table('subscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('subscriptions', 'pppoe_username')) {
                $table->string('pppoe_username')->nullable()->unique()->after('notes');
            }
            if (!Schema::hasColumn('subscriptions', 'pppoe_password')) {
                $table->string('pppoe_password')->nullable()->after('pppoe_username');
            }
            if (!Schema::hasColumn('subscriptions', 'olt_id')) {
                $table->foreignId('olt_id')->nullable()->constrained('olts')->nullOnDelete()->after('pppoe_password');
            }
            if (!Schema::hasColumn('subscriptions', 'olt_frame')) {
                $table->string('olt_frame')->nullable()->after('olt_id');
            }
            if (!Schema::hasColumn('subscriptions', 'olt_slot')) {
                $table->string('olt_slot')->nullable()->after('olt_frame');
            }
            if (!Schema::hasColumn('subscriptions', 'olt_port')) {
                $table->string('olt_port')->nullable()->after('olt_slot');
            }
            if (!Schema::hasColumn('subscriptions', 'olt_onu_id')) {
                $table->string('olt_onu_id')->nullable()->after('olt_port');
            }
            if (!Schema::hasColumn('subscriptions', 'device_sn')) {
                $table->string('device_sn')->nullable()->after('olt_onu_id');
            }
            if (!Schema::hasColumn('subscriptions', 'service_vlan')) {
                $table->integer('service_vlan')->nullable()->after('device_sn');
            }
            if (!Schema::hasColumn('subscriptions', 'last_online_at')) {
                $table->timestamp('last_online_at')->nullable();
            }
            if (!Schema::hasColumn('subscriptions', 'provisioned_at')) {
                $table->timestamp('provisioned_at')->nullable();
            }
            if (!Schema::hasColumn('subscriptions', 'last_provisioning_log')) {
                $table->json('last_provisioning_log')->nullable();
            }
        });

        Schema::table('packages', function (Blueprint $table) {
            if (!Schema::hasColumn('packages', 'olt_profile_name')) {
                $table->string('olt_profile_name')->nullable()->after('price');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('olt_profile_name');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign(['olt_id']);
            $table->dropColumn([
                'pppoe_username',
                'pppoe_password',
                'olt_id',
                'olt_frame',
                'olt_slot',
                'olt_port',
                'olt_onu_id',
                'device_sn',
                'service_vlan',
                'last_online_at',
                'provisioned_at',
                'last_provisioning_log'
            ]);
        });

        Schema::dropIfExists('olts');
    }
};
