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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2)->comment('Montant du paiement');
            $table->enum('payment_method', ['cash', 'mobile_money', 'bank_transfer', 'check'])
                ->default('cash')
                ->comment('Méthode de paiement');
            $table->date('payment_date')->comment('Date du paiement');
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->comment('Utilisateur ayant enregistré le paiement');
            $table->timestamps();

            // Index pour requêtes rapides
            $table->index('sale_id');
            $table->index('payment_date');
            $table->index(['sale_id', 'payment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
