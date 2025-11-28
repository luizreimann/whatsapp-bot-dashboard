<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('whatsapp_instances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();

            $table->string('status')->default('inactive'); 
            // inactive, pending_local, starting, qr_ready, connected, disconnected, error

            $table->string('bot_token')->unique();

            $table->string('number')->nullable();

            $table->string('fly_app_name')->nullable();
            $table->string('public_url')->nullable();
            $table->json('last_status_payload')->nullable();
            $table->timestamp('last_connected_at')->nullable();
            $table->timestamps();

            $table->index('number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_instances');
    }
};
