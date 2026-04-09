<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Adds indexes for Dashboard queries that filter on paid_at, created_at,
     * and used_at columns. These queries run on every Dashboard load.
     */
    public function up(): void
    {
        // Index for Invoice revenue queries (Dashboard):
        // Invoice::where('status', 'paid')->whereMonth('paid_at', ...)->sum('total')
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasIndex('invoices', 'idx_invoices_paid_at')) {
                $table->index('paid_at', 'idx_invoices_paid_at');
            }
        });

        // Index for "new customers this month" query (Dashboard):
        // Customer::whereMonth('created_at', ...)->count()
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasIndex('customers', 'idx_customers_created_at')) {
                $table->index('created_at', 'idx_customers_created_at');
            }
        });

        // Index for Hotspot revenue chart (Dashboard):
        // HotspotVoucher::whereNotNull('used_at')->whereMonth('used_at', ...)->sum()
        if (Schema::hasTable('hotspot_vouchers')) {
            Schema::table('hotspot_vouchers', function (Blueprint $table) {
                if (!Schema::hasIndex('hotspot_vouchers', 'idx_hotspot_vouchers_used_at')) {
                    $table->index('used_at', 'idx_hotspot_vouchers_used_at');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasIndex('invoices', 'idx_invoices_paid_at')) {
                $table->dropIndex('idx_invoices_paid_at');
            }
        });

        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasIndex('customers', 'idx_customers_created_at')) {
                $table->dropIndex('idx_customers_created_at');
            }
        });

        if (Schema::hasTable('hotspot_vouchers')) {
            Schema::table('hotspot_vouchers', function (Blueprint $table) {
                if (Schema::hasIndex('hotspot_vouchers', 'idx_hotspot_vouchers_used_at')) {
                    $table->dropIndex('idx_hotspot_vouchers_used_at');
                }
            });
        }
    }
};
