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
        // 1. Local Database
        if (Schema::hasTable('nas')) {
            Schema::table('nas', function (Blueprint $table) {
                if (!Schema::hasColumn('nas', 'require_message_authenticator')) {
                    $table->boolean('require_message_authenticator')->default(false)->after('description');
                }
            });
        }

        // 2. Radius Database
        if (Schema::connection('radius')->hasTable('nas')) {
            Schema::connection('radius')->table('nas', function (Blueprint $table) {
                // Check if column exists in radius db
                if (!Schema::connection('radius')->hasColumn('nas', 'require_message_authenticator')) {
                    // Note: Radius schema often uses specific types, but typically TinyInt(1) or ENUM. 
                    // We'll use boolean which maps to TinyInt(1) in MySQL.
                    // The field name might need to match what FreeRadius expects if using standard schema.
                    // However, custom queries in FreeRadius can map this.
                    // Standard FreeRadius schema for `nas` table typically has `nasname`, `shortname`, `type`, `ports`, `secret`, `server`, `community`, `description`.
                    // It DOES NOT have `require_message_authenticator` by default.
                    // We are adding it so we can use it in the logical query or if using a custom schema.
                    // BUT WAIT -> FreeRadius usually handles this via `client` definition options.
                    // If using `sql` module for clients (read_clients = yes), the query is:
                    // client_query = "SELECT id, nasname, shortname, type, secret, server, community, description FROM ${client_table}"
                    // We might need to update the FreeRadius query configuration to include this new column if we want it to be automatically picked up, 
                    // OR we rely on the user to update their `client_query`.
                    // For now, we add the column.
                    $table->boolean('require_message_authenticator')->default(false);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Local Database
        if (Schema::hasTable('nas')) {
            Schema::table('nas', function (Blueprint $table) {
                if (Schema::hasColumn('nas', 'require_message_authenticator')) {
                    $table->dropColumn('require_message_authenticator');
                }
            });
        }

        // 2. Radius Database
        if (Schema::connection('radius')->hasTable('nas')) {
            Schema::connection('radius')->table('nas', function (Blueprint $table) {
                if (Schema::connection('radius')->hasColumn('nas', 'require_message_authenticator')) {
                    $table->dropColumn('require_message_authenticator');
                }
            });
        }
    }
};
