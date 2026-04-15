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
        Schema::create('script_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('brand'); // zte, huawei, etc.
            $table->string('type'); // activation, restriction, unblock, etc.
            $table->text('content'); // The script template content
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('script_templates');
    }
};
