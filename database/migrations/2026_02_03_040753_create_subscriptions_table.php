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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            
            $table->string('status', 30)->default('pending');
            
            $table->string('payment_method', 50)->nullable();
            $table->string('external_subscription_id')->nullable();
            $table->string('external_customer_id')->nullable();
            
            $table->string('billing_cycle', 20)->default('monthly');
            $table->date('current_period_start')->nullable();
            $table->date('current_period_end')->nullable();
            
            $table->decimal('amount', 10, 2)->default(297.00);
            $table->string('currency', 3)->default('BRL');
            
            $table->timestamp('canceled_at')->nullable();
            $table->timestamp('suspended_at')->nullable();
            
            $table->timestamps();
            
            $table->unique('tenant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
