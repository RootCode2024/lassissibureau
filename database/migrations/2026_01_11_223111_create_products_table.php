<?php

use App\Enums\ProductStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_model_id')->constrained()->cascadeOnDelete();

            // IMEI unique (obligatoire pour téléphones)
            $table->string('imei')->unique()->nullable()->comment('IMEI pour téléphones');
            $table->string('serial_number')->nullable()->comment('Numéro série pour accessoires');

            // État actuel
            $table->string('status')->default(ProductStatus::STOCK_BOUTIQUE->value);

            // Prix spécifiques à ce produit (peut différer du modèle)
            $table->decimal('prix_achat', 10, 2)->comment('Prix réel d\'achat de ce produit');
            $table->decimal('prix_vente', 10, 2)->comment('Prix de vente actuel');

            // Informations d'achat
            $table->date('date_achat')->nullable();
            $table->string('fournisseur')->nullable();
            $table->text('notes')->nullable();

            // Condition physique
            $table->string('condition')->nullable()->comment('Neuf, Excellent, Bon, Correct, etc.');
            $table->text('defauts')->nullable()->comment('Défauts constatés');

            // Traçabilité
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Index pour performance
            $table->index('status');
            $table->index('date_achat');
            $table->index(['product_model_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
