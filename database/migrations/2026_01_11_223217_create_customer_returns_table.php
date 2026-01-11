<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_returns', function (Blueprint $table) {
            $table->id();

            // Vente originale
            $table->foreignId('original_sale_id')->constrained('sales')->cascadeOnDelete();

            // Produit retourné
            $table->foreignId('returned_product_id')->constrained('products')->cascadeOnDelete();

            // Produit d'échange (si échange)
            $table->foreignId('exchange_product_id')->nullable()->constrained('products')->nullOnDelete();

            // Nouvelle vente créée pour l'échange
            $table->foreignId('exchange_sale_id')->nullable()->constrained('sales')->nullOnDelete();

            // Raison du retour
            $table->text('reason')->comment('Motif du retour');
            $table->text('defect_description')->nullable()->comment('Description du défaut constaté');

            // Remboursement ou échange
            $table->boolean('is_exchange')->default(false)->comment('True si échange, false si remboursement');
            $table->decimal('refund_amount', 10, 2)->default(0)->comment('Montant remboursé si pas d\'échange');

            // Traçabilité
            $table->foreignId('processed_by')->constrained('users')->cascadeOnDelete();

            $table->timestamps();

            // Index
            $table->index('original_sale_id');
            $table->index('returned_product_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_returns');
    }
};
