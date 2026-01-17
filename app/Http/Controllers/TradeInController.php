<?php

namespace App\Http\Controllers;

use App\Models\ProductModel;
use App\Models\TradeIn;
use App\Services\SaleService;
use Illuminate\Http\Request;

class TradeInController extends Controller
{
    public function __construct(
        private SaleService $saleService
    ) {}

    /**
     * Liste des trocs en attente de traitement
     */
    public function pending()
    {
        $this->authorize('viewAny', TradeIn::class);

        $tradeIns = TradeIn::with(['sale.product.productModel', 'sale.seller'])
            ->pending()
            ->latest()
            ->paginate(20);

        return view('trade-ins.pending', compact('tradeIns'));
    }

    /**
     * Afficher le formulaire de création du produit reçu
     */
    public function create(TradeIn $tradeIn)
    {
        $this->authorize('create', TradeIn::class);

        if ($tradeIn->hasProductReceived()) {
            return redirect()
                ->route('trade-ins.pending')
                ->with('error', 'Ce troc a déjà été traité.');
        }

        $productModels = ProductModel::orderBy('brand')->orderBy('name')->get();

        return view('trade-ins.create-product', compact('tradeIn', 'productModels'));
    }

    /**
     * Créer le produit reçu en troc
     */
    public function storeProduct(Request $request, TradeIn $tradeIn)
    {
        $this->authorize('create', TradeIn::class);

        if ($tradeIn->hasProductReceived()) {
            return redirect()
                ->route('trade-ins.pending')
                ->with('error', 'Ce troc a déjà été traité.');
        }

        $validated = $request->validate([
            'product_model_id' => 'required|exists:product_models,id',
            'prix_vente' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        try {
            $product = $this->saleService->createTradeInProduct(
                $tradeIn,
                $validated['product_model_id'],
                $validated['prix_vente'] ?? null,
                $validated['notes'] ?? null
            );

            return redirect()
                ->route('products.show', $product)
                ->with('success', 'Produit reçu en troc créé avec succès.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erreur : '.$e->getMessage());
        }
    }
}
