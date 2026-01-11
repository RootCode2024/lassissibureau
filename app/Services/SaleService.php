<?php

namespace App\Services;

use App\Models\Sale;
use App\Enums\SaleType;
use App\Models\Product;
use App\Models\TradeIn;
use App\Enums\ProductStatus;
use App\Enums\StockMovementType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SaleService
{
    public function __construct(
        private ProductService $productService,
        private StockService $stockService
    ) {}

    /**
     * Créer une vente (avec ou sans troc).
     */
    public function createSale(array $data): Sale
    {
        return DB::transaction(function () use ($data) {
            $product = Product::findOrFail($data['product_id']);

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
                'sold_by' => $data['sold_by'],
                'notes' => $data['notes'] ?? null,
            ]);

            // Gérer le troc si présent
            if (isset($data['has_trade_in']) && $data['has_trade_in']) {
                $this->handleTradeIn($sale, $data['trade_in'], $data['sold_by']);
            }

            // Changer le statut du produit vendu
            $newStatus = $data['reseller_id']
                ? ProductStatus::CHEZ_REVENDEUR
                : ProductStatus::VENDU;

            $movementType = $data['reseller_id']
                ? StockMovementType::DEPOT_REVENDEUR
                : ($data['sale_type'] === SaleType::ACHAT_DIRECT->value
                    ? StockMovementType::VENTE_DIRECTE
                    : StockMovementType::VENTE_TROC);

            $this->stockService->createMovement([
                'product_id' => $product->id,
                'type' => $movementType->value,
                'quantity' => 1,
                'status_after' => $newStatus->value,
                'sale_id' => $sale->id,
                'reseller_id' => $data['reseller_id'] ?? null,
                'user_id' => $data['sold_by'],
                'notes' => 'Vente créée',
            ]);

            return $sale->fresh(['product.productModel', 'tradeIn', 'reseller']);
        });
    }

    /**
     * Gérer le troc (produit repris).
     */
    private function handleTradeIn(Sale $sale, array $tradeInData, int $userId): void
    {
        // Créer le produit reçu en troc
        $tradeInProduct = $this->productService->createProduct([
            'product_model_id' => null, // À définir manuellement après
            'imei' => $tradeInData['imei_recu'],
            'status' => ProductStatus::STOCK_BOUTIQUE->value,
            'prix_achat' => $tradeInData['valeur_reprise'],
            'prix_vente' => $tradeInData['valeur_reprise'] * 1.2, // Marge par défaut 20%
            'date_achat' => now(),
            'notes' => 'Reçu en troc - ' . $tradeInData['modele_recu'],
            'condition' => $tradeInData['condition'] ?? null,
            'defauts' => $tradeInData['etat_recu'] ?? null,
            'created_by' => $userId,
        ]);

        // Créer l'enregistrement du troc
        TradeIn::create([
            'sale_id' => $sale->id,
            'product_received_id' => $tradeInProduct->id,
            'valeur_reprise' => $tradeInData['valeur_reprise'],
            'complement_especes' => $tradeInData['complement_especes'] ?? 0,
            'imei_recu' => $tradeInData['imei_recu'],
            'modele_recu' => $tradeInData['modele_recu'],
            'etat_recu' => $tradeInData['etat_recu'] ?? null,
        ]);
    }

    /**
     * Confirmer une vente revendeur.
     */
    public function confirmResellerSale(Sale $sale, ?string $notes = null): Sale
    {
        return DB::transaction(function () use ($sale, $notes) {
            // Confirmer la vente
            $sale->confirm($notes);

            // Changer le statut du produit à VENDU
            $this->stockService->createMovement([
                'product_id' => $sale->product_id,
                'type' => StockMovementType::VENTE_DIRECTE->value,
                'quantity' => 1,
                'status_after' => ProductStatus::VENDU->value,
                'sale_id' => $sale->id,
                'reseller_id' => $sale->reseller_id,
                'user_id' => Auth::id(),
                'notes' => 'Confirmation vente revendeur' . ($notes ? " - {$notes}" : ''),
            ]);

            return $sale->fresh(['product', 'reseller']);
        });
    }

    /**
     * Obtenir les statistiques de vente pour une période.
     */
    public function getSalesStats($startDate, $endDate, ?int $userId = null): array
    {
        $query = Sale::confirmed()
            ->betweenDates($startDate, $endDate);

        if ($userId) {
            $query->where('sold_by', $userId);
        }

        $sales = $query->get();

        return [
            'total_sales' => $sales->count(),
            'total_revenue' => $sales->sum('prix_vente'),
            'total_profit' => $sales->sum(fn($sale) => $sale->benefice),
            'average_sale_price' => $sales->avg('prix_vente'),
            'average_profit_per_sale' => $sales->count() > 0
                ? $sales->sum(fn($sale) => $sale->benefice) / $sales->count()
                : 0,
            'sales_by_type' => $sales->groupBy('sale_type')->map->count(),
            'trade_ins_count' => $sales->where('sale_type', '!=', SaleType::ACHAT_DIRECT->value)->count(),
        ];
    }

    /**
     * Obtenir les ventes en attente (revendeurs).
     */
    public function getPendingSales()
    {
        return Sale::with(['product.productModel', 'reseller', 'seller'])
            ->pending()
            ->orderBy('date_depot_revendeur')
            ->get();
    }
}
