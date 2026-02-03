<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('integration_accounts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained()
                ->cascadeOnDelete();

            // Categoria da integração: crm, email_marketing, payment, ecommerce, traffic, support, automation
            $table->string('category', 50);

            // Provedor específico: rd_station_crm, pipedrive, mailchimp, mercado_pago, etc.
            $table->string('provider', 100);

            // Nome amigável da conexão (ex: "RD Station da Agência X")
            $table->string('name')->nullable();

            // Config / credenciais (tokens, keys etc) – encriptado depois via cast
            $table->json('config')->nullable();

            // Outros dados úteis (ex: id da conta remota, infos adicionais)
            $table->json('metadata')->nullable();

            // Status da conexão: connected, disconnected, error, pending_auth...
            $table->string('status', 50)->default('disconnected');

            $table->timestamp('connected_at')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();

            // Garante que um tenant não crie duas contas para o mesmo provider
            $table->unique(['tenant_id', 'provider']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('integration_accounts');
    }
};