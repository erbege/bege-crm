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
        Schema::create('hotspot_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('rate_limit')->nullable()->comment('e.g. 2M/2M');
            $table->integer('shared_users')->default(1);
            $table->integer('session_timeout')->nullable()->comment('Minutes');
            $table->integer('keepalive_timeout')->nullable()->comment('Minutes');
            $table->decimal('price', 10, 2)->default(0);
            $table->integer('validity_days')->default(1)->comment('Voucher validity in days');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('hotspot_vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotspot_profile_id')->constrained()->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('batch_id')->nullable();
            $table->string('status')->default('active')->comment('active, used, expired');
            $table->timestamp('used_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotspot_tables');
    }
};
