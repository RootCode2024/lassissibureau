<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trade_ins', function (Blueprint $table) {
            $table->id();

            // Vente associée
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();

            // Produit reçu en troc
            $table->foreignId('product_received_id')->constrained('products')->cascadeOnDelete();

            // Valeur du troc
            $table->decimal('valeur_reprise', 10, 2)->comment('Valeur du téléphone repris');
            $table->decimal('complement_especes', 10, 2)->default(0)->comment('Complément payé en espèces');

            // Infos du produit repris
            $table->string('imei_recu');
            $table->string('modele_recu')->comment('Modèle du téléphone repris');
            $table->text('etat_recu')->nullable()->comment('État du téléphone repris');

            $table->timestamps();

            // Index
            $table->index('sale_id');
            $table->index('imei_recu');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trade_ins');
    }
};
