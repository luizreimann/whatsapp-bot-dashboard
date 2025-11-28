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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('flux_id')
                ->nullable()
                ->constrained('fluxes')
                ->nullOnDelete();
            
            $table->string('name')->nullable();
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('source')->nullable();
            
            $table->string('status')->default('new');
            // new, contacted, converted, lost...

            $table->json('data')->nullable(); 
            $table->timestamps();

            $table->index(['tenant_id', 'phone']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
