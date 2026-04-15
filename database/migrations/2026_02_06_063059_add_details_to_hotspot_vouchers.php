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
        Schema::table('hotspot_vouchers', function (Blueprint $table) {
            $table->foreignId('nas_id')->nullable()->after('hotspot_profile_id')->constrained('nas')->nullOnDelete();
            $table->string('server')->default('all')->after('nas_id');
            $table->string('user_mode')->default('username_password')->after('server')->comment('username_password, username_equals_password');
            $table->string('time_limit')->nullable()->after('user_mode');
            $table->bigInteger('data_limit')->nullable()->after('time_limit')->comment('Bytes');
            $table->string('comment')->nullable()->after('data_limit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hotspot_vouchers', function (Blueprint $table) {
            $table->dropForeign(['nas_id']);
            $table->dropColumn(['nas_id', 'server', 'user_mode', 'time_limit', 'data_limit', 'comment']);
        });
    }
};
