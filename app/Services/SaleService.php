<?php

namespace App\Services;

use App\Models\Sale;
use App\Enums\SaleType;
use App\Models\Payment;
use App\Models\Product;
use App\Models\TradeIn;
use App\Enums\ProductState;
use App\Enums\PaymentStatus;
use App\Enums\ProductLocation;
use App\Enums\StockMovementType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SaleService
{
    public function __construct(
        private StockService $stockService
    ) {}

    /**
     * Créer une vente (avec ou sans troc).
     */
    public function createSale(array $data): Sale
    {
        return DB::transaction(function () use ($data) {
            $product = Product::findOrFail($data['product_id']);

            if (!$product->isAvailable()) {
                throw new \Exception('Ce produit n\'est pas disponible à la vente.');
            }

            // Déterminer le statut de paiement
            $paymentStatus = PaymentStatus::PAID;
            $amountPaid = $data['prix_vente'];
            $amountRemaining = 0;
            $paymentDueDate = null;

            // Si c'est un revendeur avec paiement différé
            if (isset($data['reseller_id']) && $data['reseller_id']) {
                $paymentStatus = PaymentStatus::from($data['payment_status'] ?? 'unpaid');
                $amountPaid = $data['amount_paid'] ?? 0;
                $amountRemaining = $data['prix_vente'] - $amountPaid;
                $paymentDueDate = $data['payment_due_date'] ?? now()->addDays(30);
            }

            // Créer la vente
            $sale = Sale::create([
                'product_id' => $data['product_id'],
                'sale_type' => $data['sale_type'],
                'prix_vente' => $data['prix_vente'],
                'prix_achat_produit' => $data['prix_achat_produit'],
                'client_name' => $data['client_name'] ?? null,
                'client_phone' => $data['client_phone'] ?? null,
                'reseller_id' => $data['reseller_id'] ?? null,
                'date_depot_revendeur' => $data['date_depot_revendeur'] ?? null,
                'date_vente_effective' => $data['date_vente_effective'],
                'is_confirmed' => $data['is_confirmed'],
                'payment_status' => $paymentStatus,
                'amount_paid' => $amountPaid,
                'amount_remaining' => $amountRemaining,
                'payment_due_date' => $paymentDueDate,
                'sold_by' => $data['sold_by'],
                'notes' => $data['notes'] ?? null,
            ]);

            // Enregistrer le paiement initial si montant > 0
            if ($amountPaid > 0) {
                $this->recordPayment($sale, $amountPaid, [
                    'payment_method' => $data['payment_method'] ?? 'cash',
                    'notes' => 'Paiement initial',
                ]);
            }

            // Gérer le troc si présent
            if (isset($data['has_trade_in']) && $data['has_trade_in'] && $data['sale_type'] === SaleType::TROC->value) {
                $this->handleTradeIn($sale, $data['trade_in']);
            }

            // Déterminer état et localisation
            if (isset($data['reseller_id']) && $data['reseller_id']) {
                $newState = ProductState::DISPONIBLE;
                $newLocation = ProductLocation::CHEZ_REVENDEUR;
                $movementType = StockMovementType::DEPOT_REVENDEUR;
            } else {
                $newState = ProductState::VENDU;
                $newLocation = ProductLocation::CHEZ_CLIENT;
                $movementType = $data['sale_type'] === SaleType::ACHAT_DIRECT->value
                    ? StockMovementType::VENTE_DIRECTE
                    : StockMovementType::VENTE_TROC;
            }

            $product->changeStateAndLocation(
                $movementType->value,
                $newState,
                $newLocation,
                $data['sold_by'],
                [
                    'sale_id' => $sale->id,
                    'reseller_id' => $data['reseller_id'] ?? null,
                    'notes' => 'Vente créée - ' . $sale->sale_type->label(),
                ]
            );

            return $sale->fresh(['product.productModel', 'tradeIn', 'reseller', 'seller', 'payments']);
        });
    }

    /**
     * Gérer le troc (produit repris).
     * Note: Le produit reçu en troc n'est PAS créé automatiquement
     * car il nécessite la sélection manuelle du product_model_id par l'admin.
     */
    private function handleTradeIn(Sale $sale, array $tradeInData): void
    {
        TradeIn::create([
            'sale_id' => $sale->id,
            'product_received_id' => null,
            'valeur_reprise' => $tradeInData['valeur_reprise'],
            'complement_especes' => $tradeInData['complement_especes'],
            'imei_recu' => $tradeInData['imei_recu'],
            'modele_recu' => $tradeInData['modele_recu'],
            'etat_recu' => $tradeInData['etat_recu'] ?? null,
        ]);
    }

    /**
     * Enregistrer un paiement
     */
    public function recordPayment(Sale $sale, float $amount, array $data = []): Payment
    {
        return DB::transaction(function () use ($sale, $amount, $data) {
            // Créer le paiement
            $payment = Payment::create([
                'sale_id' => $sale->id,
                'amount' => $amount,
                'payment_method' => $data['payment_method'] ?? 'cash',
                'payment_date' => $data['payment_date'] ?? now(),
                'notes' => $data['notes'] ?? null,
                'recorded_by' => Auth::id(),
            ]);

            // Mettre à jour le statut de paiement de la vente
            $newAmountPaid = $sale->amount_paid + $amount;
            $newAmountRemaining = $sale->prix_vente - $newAmountPaid;

            $newStatus = $newAmountRemaining <= 0
                ? PaymentStatus::PAID
                : ($newAmountPaid > 0 ? PaymentStatus::PARTIAL : PaymentStatus::UNPAID);

            $sale->update([
                'amount_paid' => $newAmountPaid,
                'amount_remaining' => max(0, $newAmountRemaining),
                'payment_status' => $newStatus,
                'final_payment_date' => $newStatus === PaymentStatus::PAID ? now() : null,
            ]);

            return $payment->fresh(['sale', 'recorder']);
        });
    }

    /**
     * Créer le produit repris dans un troc.
     */
    public function createTradeInProduct(
        TradeIn $tradeIn,
        int $productModelId,
        ?float $prixVente = null,
        ?string $notes = null
    ): Product {
        return DB::transaction(function () use ($tradeIn, $productModelId, $prixVente, $notes) {
            // Calculer le prix de vente si non fourni (marge 20%)
            $calculatedPrixVente = $prixVente ?? ($tradeIn->valeur_reprise * 1.2);

            // Créer le produithandleTradeIn
            $product = Product::create([
                'product_model_id' => $productModelId,
                'imei' => $tradeIn->imei_recu,
                'state' => ProductState::DISPONIBLE->value,
                'location' => ProductLocation::BOUTIQUE->value,
                'prix_achat' => $tradeIn->valeur_reprise,
                'prix_vente' => $calculatedPrixVente,
                'date_achat' => now(),
                'notes' => $notes ?? 'Reçu en troc - Vente #' . $tradeIn->sale_id,
                'condition' => 'troc',
                'defauts' => $tradeIn->etat_recu,
                'created_by' => Auth::id(),
            ]);

            // Lier le produit au troc
            $tradeIn->update(['product_received_id' => $product->id]);

            // Créer le mouvement de stock
            $this->stockService->createMovement([
                'product_id' => $product->id,
                'type' => StockMovementType::TROC_RECU->value,
                'quantity' => 1,
                'state_before' => null,
                'location_before' => null,
                'state_after' => ProductState::DISPONIBLE->value,
                'location_after' => ProductLocation::BOUTIQUE->value,
                'user_id' => Auth::id(),
                'notes' => 'Produit reçu en troc - Vente #' . $tradeIn->sale_id,
            ]);

            event(new \App\Events\TradeInProcessed($tradeIn));

            return $product->fresh('productModel');
        });
    }


    /**
     * Confirmer une vente revendeur.
     */
    public function confirmResellerSale(Sale $sale, array $data = []): Sale
    {
        return DB::transaction(function () use ($sale, $data) {
            if ($sale->is_confirmed) {
                throw new \Exception('Cette vente est déjà confirmée.');
            }

            if (!$sale->reseller_id) {
                throw new \Exception('Cette vente n\'est pas une vente revendeur.');
            }

            // Si un paiement est fourni, l'enregistrer
            if (isset($data['payment_amount']) && $data['payment_amount'] > 0) {
                $this->recordPayment($sale, $data['payment_amount'], [
                    'payment_method' => $data['payment_method'] ?? 'cash',
                    'notes' => 'Paiement lors de la confirmation',
                ]);
            }

            // Confirmer la vente
            $sale->update([
                'is_confirmed' => true,
                'date_confirmation_vente' => now(),
                'notes' => $data['notes'] ?? $sale->notes,
            ]);

            // Changer l'état du produit à VENDU
            $sale->product->changeStateAndLocation(
                StockMovementType::VENTE_DIRECTE->value,
                ProductState::VENDU,
                ProductLocation::CHEZ_CLIENT,
                Auth::id(),
                [
                    'sale_id' => $sale->id,
                    'reseller_id' => $sale->reseller_id,
                    'notes' => 'Confirmation vente revendeur',
                ]
            );

            return $sale->fresh(['product.productModel', 'reseller', 'seller', 'payments']);
        });
    }

    /**
     * Retourner un produit du revendeur au stock.
     */
    public function returnFromReseller(Sale $sale, string $reason): Sale
    {
        return DB::transaction(function () use ($sale, $reason) {
            if ($sale->is_confirmed) {
                throw new \Exception('Impossible de retourner une vente déjà confirmée.');
            }

            if (!$sale->reseller_id) {
                throw new \Exception('Cette vente n\'est pas une vente revendeur.');
            }

            // Si des paiements ont été faits, ils doivent être remboursés
            if ($sale->amount_paid > 0) {
                $sale->update([
                    'notes' => ($sale->notes ? $sale->notes . "\n" : '')
                        . "RETOUR REVENDEUR: {$reason}\n"
                        . "Montant à rembourser: " . number_format($sale->amount_paid, 0, ',', ' ') . " FCFA",
                ]);
            } else {
                $sale->update([
                    'notes' => ($sale->notes ? $sale->notes . "\n" : '') . "RETOUR REVENDEUR: " . $reason,
                ]);
            }

            // Ramener le produit en stock
            $sale->product->changeStateAndLocation(
                StockMovementType::RETOUR_REVENDEUR->value,
                ProductState::DISPONIBLE,
                ProductLocation::BOUTIQUE,
                Auth::id(),
                [
                    'sale_id' => $sale->id,
                    'reseller_id' => $sale->reseller_id,
                    'notes' => 'Retour du revendeur: ' . $reason,
                ]
            );

            // Supprimer la vente (soft delete)
            $sale->delete();

            return $sale->fresh(['product.productModel', 'reseller', 'seller', 'payments']);
        });
    }

    /**
     * Obtenir les ventes impayées ou partiellement payées
     */
    public function getUnpaidSales(?int $resellerId = null)
    {
        $query = Sale::with(['product.productModel', 'reseller', 'seller', 'payments'])
            ->whereIn('payment_status', [PaymentStatus::UNPAID, PaymentStatus::PARTIAL])
            ->orderBy('payment_due_date', 'asc');

        if ($resellerId) {
            $query->where('reseller_id', $resellerId);
        }

        return $query->get();
    }

    /**
     * Obtenir les statistiques de paiement
     */
    public function getPaymentStats(?int $resellerId = null): array
    {
        $query = Sale::confirmed();

        if ($resellerId) {
            $query->where('reseller_id', $resellerId);
        }

        $sales = $query->get();

        return [
            'total_sales_amount' => $sales->sum('prix_vente'),
            'total_paid' => $sales->sum('amount_paid'),
            'total_remaining' => $sales->sum('amount_remaining'),
            'unpaid_count' => $sales->where('payment_status', PaymentStatus::UNPAID)->count(),
            'partial_count' => $sales->where('payment_status', PaymentStatus::PARTIAL)->count(),
            'paid_count' => $sales->where('payment_status', PaymentStatus::PAID)->count(),
        ];
    }

    /**
     * Obtenir les statistiques de vente pour une période.
     */
    public function getSalesStats($startDate, $endDate, ?int $userId = null): array
    {
        $query = Sale::confirmed()->betweenDates($startDate, $endDate);

        if ($userId) {
            $query->where('sold_by', $userId);
        }

        $sales = $query->get();

        return [
            'total_sales' => $sales->count(),
            'total_revenue' => $sales->sum('prix_vente'),
            'total_profit' => $sales->sum(fn($sale) => $sale->benefice),
            'average_sale_price' => $sales->avg('prix_vente') ?? 0,
            'average_profit_per_sale' => $sales->count() > 0
                ? $sales->sum(fn($sale) => $sale->benefice) / $sales->count()
                : 0,
            'sales_by_type' => $sales->groupBy('sale_type')->map->count()->toArray(),
        ];
    }

    /**
     * Obtenir les ventes en attente (revendeurs).
     */
    public function getPendingSales()
    {
        return Sale::with(['product.productModel', 'reseller', 'seller'])
            ->pending()
            ->orderBy('date_depot_revendeur', 'desc')
            ->get();
    }

    /**
     * Obtenir les produits actuellement chez les revendeurs.
     */
    public function getProductsAtResellers(?int $resellerId = null)
    {
        $query = Product::where('location', ProductLocation::CHEZ_REVENDEUR->value)
            ->where('state', ProductState::DISPONIBLE->value)
            ->with(['productModel', 'currentSale.reseller']);

        if ($resellerId) {
            $query->whereHas('currentSale', function ($q) use ($resellerId) {
                $q->where('reseller_id', $resellerId)
                    ->where('is_confirmed', false);
            });
        }

        return $query->get();
    }

    /**
     * Obtenir les trocs sans produit créé.
     */
    public function getTradeInsWithoutProduct()
    {
        return TradeIn::with(['sale.product.productModel', 'sale.seller'])
            ->whereNull('product_received_id')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obtenir les statistiques d'un vendeur.
     */
    public function getSellerStats(int $userId, $startDate = null, $endDate = null): array
    {
        $query = Sale::confirmed()->where('sold_by', $userId);

        if ($startDate) {
            $query->where('date_vente_effective', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('date_vente_effective', '<=', $endDate);
        }

        $sales = $query->get();

        return [
            'total_sales' => $sales->count(),
            'total_revenue' => $sales->sum('prix_vente'),
            'today_sales' => Sale::confirmed()->where('sold_by', $userId)->today()->count(),
            'week_sales' => Sale::confirmed()->where('sold_by', $userId)->thisWeek()->count(),
            'month_sales' => Sale::confirmed()->where('sold_by', $userId)->thisMonth()->count(),
        ];
    }
}
