<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_models', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Ex: iPhone 13 Pro Max 256GB
            $table->string('brand')->nullable(); // Apple, Samsung, etc.
            $table->text('description')->nullable();
            $table->string('category')->default('telephone'); // telephone, accessoire
            $table->string('image_url')->nullable();

            // Prix de référence (peut être override par produit)
            $table->decimal('prix_revient_default', 10, 2)->nullable()->comment('Prix de revient par défaut');
            $table->decimal('prix_vente_default', 10, 2)->nullable()->comment('Prix de vente par défaut');

            // Stock minimum pour alerte
            $table->integer('stock_minimum')->default(0);

            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index('category');
            $table->index('brand');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_models');
    }
};
