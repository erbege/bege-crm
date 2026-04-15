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
        Schema::create('nas_servers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nas_id')->constrained('nas')->onDelete('cascade');
            $table->enum('type', ['pppoe', 'hotspot', 'dhcp']);
            $table->string('name');
            $table->string('interface')->nullable();
            $table->string('profile')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nas_servers');
    }
};
