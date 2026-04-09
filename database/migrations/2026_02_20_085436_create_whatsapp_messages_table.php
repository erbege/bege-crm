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
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->string('target'); // Phone number
            $table->text('message')->nullable();
            $table->string('template_name')->nullable();
            $table->json('template_data')->nullable(); // Store JSON data for template variables
            $table->string('status')->default('pending'); // pending, sent, failed, scheduled
            $table->string('provider')->nullable(); // fonnte, etc.
            $table->json('response')->nullable(); // API Response
            $table->text('error')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
    }
};
