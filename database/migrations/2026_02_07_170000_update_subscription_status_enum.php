<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'active', 'suspended', 'terminated' to the enum
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE subscriptions MODIFY COLUMN status ENUM('unpaid', 'paid', 'partial', 'cancelled', 'active', 'suspended', 'terminated') DEFAULT 'unpaid'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum (WARNING: this might fail if data exists with new statuses)
        // We generally shouldn't lose data in down(), but for strict reversion:
        DB::statement("ALTER TABLE subscriptions MODIFY COLUMN status ENUM('unpaid', 'paid', 'partial', 'cancelled') DEFAULT 'unpaid'");
    }
};
