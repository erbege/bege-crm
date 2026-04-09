<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration adds indexes for improved query performance based on
     * common query patterns in the application.
     */
    public function up(): void
    {
        // Customers table indexes
        Schema::table('customers', function (Blueprint $table) {
            // Index for phone search (used in customer lookup)
            if (!Schema::hasIndex('customers', 'idx_customers_phone')) {
                $table->index('phone', 'idx_customers_phone');
            }

            // Composite index for name search patterns
            if (!Schema::hasIndex('customers', 'idx_customers_name_customer')) {
                $table->index(['name', 'customer_id'], 'idx_customers_name_customer');
            }

            // Index for registration date filtering
            if (!Schema::hasIndex('customers', 'idx_customers_registered_at')) {
                $table->index('registered_at', 'idx_customers_registered_at');
            }
        });

        // Subscriptions table indexes
        Schema::table('subscriptions', function (Blueprint $table) {
            // Composite index for customer + status queries
            if (!Schema::hasIndex('subscriptions', 'idx_subscriptions_customer_status')) {
                $table->index(['customer_id', 'status'], 'idx_subscriptions_customer_status');
            }

            // Composite index for period end + status (for overdue checks)
            if (!Schema::hasIndex('subscriptions', 'idx_subscriptions_period_status')) {
                $table->index(['period_end', 'status'], 'idx_subscriptions_period_status');
            }

            // Index for PPPoE username lookups (Radius sync)
            if (!Schema::hasIndex('subscriptions', 'idx_subscriptions_pppoe_username')) {
                $table->index('pppoe_username', 'idx_subscriptions_pppoe_username');
            }

            // Index for NAS-based queries
            if (!Schema::hasIndex('subscriptions', 'idx_subscriptions_nas_id')) {
                $table->index('nas_id', 'idx_subscriptions_nas_id');
            }
        });

        // Coverage points table indexes
        Schema::table('coverage_points', function (Blueprint $table) {
            // Composite index for filtering by area, type, and active status
            if (!Schema::hasIndex('coverage_points', 'idx_coverage_points_area_type_active')) {
                $table->index(['area_id', 'type', 'is_active'], 'idx_coverage_points_area_type_active');
            }

            // Index for capacity-based queries
            if (!Schema::hasIndex('coverage_points', 'idx_coverage_points_type_capacity')) {
                $table->index(['type', 'capacity'], 'idx_coverage_points_type_capacity');
            }
        });

        // Invoices table indexes
        Schema::table('invoices', function (Blueprint $table) {
            // Composite index for date + status filtering
            if (!Schema::hasIndex('invoices', 'idx_invoices_date_status')) {
                $table->index(['issue_date', 'status'], 'idx_invoices_date_status');
            }

            // Index for invoice number lookups
            if (!Schema::hasIndex('invoices', 'idx_invoices_number')) {
                $table->index('invoice_number', 'idx_invoices_number');
            }

            // Index for customer_id on invoices
            if (!Schema::hasIndex('invoices', 'idx_invoices_customer_id')) {
                $table->index('customer_id', 'idx_invoices_customer_id');
            }
        });

        // Hotspot vouchers table indexes (if exists)
        if (Schema::hasTable('hotspot_vouchers')) {
            Schema::table('hotspot_vouchers', function (Blueprint $table) {
                if (!Schema::hasIndex('hotspot_vouchers', 'idx_hotspot_vouchers_code')) {
                    $table->index('code', 'idx_hotspot_vouchers_code');
                }

                if (!Schema::hasIndex('hotspot_vouchers', 'idx_hotspot_vouchers_profile_status')) {
                    $table->index(['hotspot_profile_id', 'status'], 'idx_hotspot_vouchers_profile_status');
                }
            });
        }

        // NAS table indexes
        if (Schema::hasTable('nas')) {
            Schema::table('nas', function (Blueprint $table) {
                if (!Schema::hasIndex('nas', 'idx_nas_shortname')) {
                    $table->index('shortname', 'idx_nas_shortname');
                }

                if (!Schema::hasIndex('nas', 'idx_nas_ip_address')) {
                    $table->index('ip_address', 'idx_nas_ip_address');
                }

                if (!Schema::hasIndex('nas', 'idx_nas_is_active')) {
                    $table->index('is_active', 'idx_nas_is_active');
                }
            });
        }

        // Packages table indexes
        if (Schema::hasTable('packages')) {
            Schema::table('packages', function (Blueprint $table) {
                if (!Schema::hasIndex('packages', 'idx_packages_is_active')) {
                    $table->index('is_active', 'idx_packages_is_active');
                }

                if (!Schema::hasIndex('packages', 'idx_packages_bw_profile_id')) {
                    $table->index('bw_profile_id', 'idx_packages_bw_profile_id');
                }
            });
        }

        // Areas table indexes
        if (Schema::hasTable('areas')) {
            Schema::table('areas', function (Blueprint $table) {
                if (!Schema::hasIndex('areas', 'idx_areas_parent_type_active')) {
                    $table->index(['parent_id', 'type', 'is_active'], 'idx_areas_parent_type_active');
                }
            });
        }

        // OLT table indexes
        // Note: OLT table doesn't have is_active column, skipping OLT indexes
        // if (Schema::hasTable('olts')) {
        //     Schema::table('olts', function (Blueprint $table) {
        //         if (!Schema::hasIndex('olts', 'idx_olts_is_active')) {
        //             $table->index('is_active', 'idx_olts_is_active');
        //         }

        //         if (!Schema::hasIndex('olts', 'idx_olts_ip_address')) {
        //             $table->index('ip_address', 'idx_olts_ip_address');
        //         }
        //     });
        // }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes in reverse order
        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasIndex('customers', 'idx_customers_phone')) {
                $table->dropIndex('idx_customers_phone');
            }
            if (Schema::hasIndex('customers', 'idx_customers_name_customer')) {
                $table->dropIndex('idx_customers_name_customer');
            }
            if (Schema::hasIndex('customers', 'idx_customers_registered_at')) {
                $table->dropIndex('idx_customers_registered_at');
            }
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            if (Schema::hasIndex('subscriptions', 'idx_subscriptions_customer_status')) {
                $table->dropIndex('idx_subscriptions_customer_status');
            }
            if (Schema::hasIndex('subscriptions', 'idx_subscriptions_period_status')) {
                $table->dropIndex('idx_subscriptions_period_status');
            }
            if (Schema::hasIndex('subscriptions', 'idx_subscriptions_pppoe_username')) {
                $table->dropIndex('idx_subscriptions_pppoe_username');
            }
            if (Schema::hasIndex('subscriptions', 'idx_subscriptions_nas_id')) {
                $table->dropIndex('idx_subscriptions_nas_id');
            }
        });

        Schema::table('coverage_points', function (Blueprint $table) {
            if (Schema::hasIndex('coverage_points', 'idx_coverage_points_area_type_active')) {
                $table->dropIndex('idx_coverage_points_area_type_active');
            }
            if (Schema::hasIndex('coverage_points', 'idx_coverage_points_type_capacity')) {
                $table->dropIndex('idx_coverage_points_type_capacity');
            }
        });

        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasIndex('invoices', 'idx_invoices_date_status')) {
                $table->dropIndex('idx_invoices_date_status');
            }
            if (Schema::hasIndex('invoices', 'idx_invoices_number')) {
                $table->dropIndex('idx_invoices_number');
            }
            if (Schema::hasIndex('invoices', 'idx_invoices_customer_id')) {
                $table->dropIndex('idx_invoices_customer_id');
            }
        });

        if (Schema::hasTable('hotspot_vouchers')) {
            Schema::table('hotspot_vouchers', function (Blueprint $table) {
                if (Schema::hasIndex('hotspot_vouchers', 'idx_hotspot_vouchers_code')) {
                    $table->dropIndex('idx_hotspot_vouchers_code');
                }
                if (Schema::hasIndex('hotspot_vouchers', 'idx_hotspot_vouchers_profile_status')) {
                    $table->dropIndex('idx_hotspot_vouchers_profile_status');
                }
            });
        }

        if (Schema::hasTable('nas')) {
            Schema::table('nas', function (Blueprint $table) {
                if (Schema::hasIndex('nas', 'idx_nas_shortname')) {
                    $table->dropIndex('idx_nas_shortname');
                }
                if (Schema::hasIndex('nas', 'idx_nas_ip_address')) {
                    $table->dropIndex('idx_nas_ip_address');
                }
                if (Schema::hasIndex('nas', 'idx_nas_is_active')) {
                    $table->dropIndex('idx_nas_is_active');
                }
            });
        }

        if (Schema::hasTable('packages')) {
            Schema::table('packages', function (Blueprint $table) {
                if (Schema::hasIndex('packages', 'idx_packages_is_active')) {
                    $table->dropIndex('idx_packages_is_active');
                }
                if (Schema::hasIndex('packages', 'idx_packages_bw_profile_id')) {
                    $table->dropIndex('idx_packages_bw_profile_id');
                }
            });
        }

        if (Schema::hasTable('areas')) {
            Schema::table('areas', function (Blueprint $table) {
                if (Schema::hasIndex('areas', 'idx_areas_parent_type_active')) {
                    $table->dropIndex('idx_areas_parent_type_active');
                }
            });
        }

        if (Schema::hasTable('olts')) {
            Schema::table('olts', function (Blueprint $table) {
                if (Schema::hasIndex('olts', 'idx_olts_is_active')) {
                    $table->dropIndex('idx_olts_is_active');
                }
                if (Schema::hasIndex('olts', 'idx_olts_ip_address')) {
                    $table->dropIndex('idx_olts_ip_address');
                }
            });
        }
    }
};
