<?php

use App\Enums\SaleType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();

            // Produit vendu
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            // Type de vente
            $table->string('sale_type')->default(SaleType::ACHAT_DIRECT->value);

            // Prix et montants
            $table->decimal('prix_vente', 10, 2)->comment('Prix de vente final');
            $table->decimal('prix_achat_produit', 10, 2)->comment('Prix d\'achat du produit (pour calcul bénéfice)');
            $table->decimal('benefice', 10, 2)->storedAs('prix_vente - prix_achat_produit');

            // Informations client
            $table->string('client_name')->nullable();
            $table->string('client_phone')->nullable();

            // Revendeur (si vente via revendeur)
            $table->foreignId('reseller_id')->nullable()->constrained()->nullOnDelete();
            $table->date('date_depot_revendeur')->nullable()->comment('Date où revendeur a pris le produit');
            $table->date('date_confirmation_vente')->nullable()->comment('Date confirmation vente par revendeur');
            $table->boolean('is_confirmed')->default(true)->comment('Vente confirmée (false si en attente chez revendeur)');

            // Statut de paiement
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])
                ->default('paid')
                ->comment('Statut du paiement');

            // Montants
            $table->decimal('amount_paid', 10, 2)
                ->default(0)
                ->comment('Montant déjà payé');

            $table->decimal('amount_remaining', 10, 2)
                ->default(0)
                ->comment('Montant restant à payer');

            // Dates de paiement
            $table->date('payment_due_date')
                ->nullable()
                ->comment('Date d\'échéance du paiement');

            $table->date('final_payment_date')
                ->nullable()
                ->comment('Date du dernier paiement (paiement complet)');

            // Date effective pour rapports (date_depot si revendeur, sinon created_at)
            $table->date('date_vente_effective')->comment('Date comptable de la vente');

            // Vendeur (utilisateur ayant effectué la vente)
            $table->foreignId('sold_by')->constrained('users')->cascadeOnDelete();

            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Index pour rapports
            $table->index('date_vente_effective');
            $table->index('sale_type');
            $table->index('reseller_id');
            $table->index('sold_by');
            $table->index(['date_vente_effective', 'is_confirmed']);
            $table->index('payment_status');
            $table->index('payment_due_date');
            $table->index(['reseller_id', 'payment_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
