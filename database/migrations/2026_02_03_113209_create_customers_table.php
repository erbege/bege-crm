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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_id')->unique(); // ID Pelanggan
            $table->string('name');
            $table->string('identity_number')->nullable(); // KTP/NIK
            $table->string('email')->nullable();
            $table->string('phone');
            $table->text('address');

            // Relations
            $table->foreignId('area_id')->constrained('areas')->onDelete('restrict');
            $table->foreignId('coverage_point_id')->nullable()->constrained('coverage_points')->nullOnDelete();
            $table->foreignId('package_id')->constrained('packages')->onDelete('restrict');

            $table->date('installation_date');
            $table->enum('status', ['active', 'inactive', 'suspended', 'isolated'])->default('active');

            // Location
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // Technical
            $table->string('pppoe_username')->nullable();
            $table->string('pppoe_password')->nullable();
            $table->string('device_sn')->nullable(); // SN Modem/ONT

            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
