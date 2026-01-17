<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();

            // Produit concerné
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            // Type de mouvement
            $table->string('type');

            // Quantité (toujours 1 pour téléphones avec IMEI, peut être > 1 pour accessoires)
            $table->integer('quantity')->default(1);

            // État du stock avant/après
            $table->string('state_before')->nullable();
            $table->string('state_after')->nullable();

            $table->string('location_before')->nullable();
            $table->string('location_after')->nullable();

            // Relations optionnelles
            $table->foreignId('sale_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('reseller_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('related_product_id')->nullable()->constrained('products')->nullOnDelete()->comment('Produit lié (ex: échange)');

            // Justification et notes
            $table->text('justification')->nullable()->comment('Obligatoire pour pertes, corrections, etc.');
            $table->text('notes')->nullable();

            // Traçabilité
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->comment('Utilisateur ayant effectué le mouvement');

            $table->timestamps();

            // Index pour performance
            $table->index('product_id');
            $table->index('type');
            $table->index('user_id');
            $table->index('created_at');
            $table->index(['product_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
