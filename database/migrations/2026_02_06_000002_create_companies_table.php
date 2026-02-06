<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('document', 20)->nullable();
            $table->string('document_type', 10)->default('cnpj');
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('segment')->nullable();
            $table->json('address')->nullable();
            $table->timestamps();

            $table->unique('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
