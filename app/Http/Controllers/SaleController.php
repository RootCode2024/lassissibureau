<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Models\Product;
use App\Models\ProductModel;
use App\Models\Reseller;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function __construct(
        private SaleService $saleService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Sale::class);

        $query = Sale::with(['product.productModel', 'seller', 'reseller']);

        // Filtrer selon le rôle
        if ($request->user()->isVendeur()) {
            $query->where('sold_by', $request->user()->id);
        }

        // Filtres
        if ($request->filled('date_from')) {
            $query->where('date_vente_effective', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('date_vente_effective', '<=', $request->date_to);
        }

        if ($request->filled('sale_type')) {
            $query->where('sale_type', $request->sale_type);
        }

        if ($request->has('is_confirmed')) {
            $query->where('is_confirmed', $request->boolean('is_confirmed'));
        }

        $sales = $query->latest('date_vente_effective')->paginate(20);

        return view('sales.index', compact('sales'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->authorize('create', Sale::class);

        // Récupérer le produit si fourni
        $product = null;
        if ($request->filled('product_id')) {
            $product = Product::with('productModel')->find($request->product_id);

            if ($product && !$product->isAvailable()) {
                return back()->with('error', 'Ce produit n\'est pas disponible à la vente.');
            }
        }

        // Produits disponibles à la vente
        $availableProducts = Product::inStock()
            ->with('productModel')
            ->get();

        // Revendeurs actifs
        $resellers = Reseller::active()->orderBy('name')->get();

        return view('sales.create', compact('product', 'availableProducts', 'resellers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSaleRequest $request)
    {
        $sale = $this->saleService->createSale($request->validated());

        return redirect()
            ->route('sales.show', $sale)
            ->with('success', 'Vente enregistrée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $sale)
    {
        $this->authorize('view', $sale);

        $sale->load(['product.productModel', 'seller', 'reseller', 'tradeIn.productReceived']);

        return view('sales.show', compact('sale'));
    }

    /**
     * Confirm a reseller sale.
     */
    public function confirm(Request $request, Sale $sale)
    {
        $this->authorize('confirm', $sale);

        $request->validate([
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $sale = $this->saleService->confirmResellerSale($sale, $request->notes);

        return back()->with('success', 'Vente confirmée avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale)
    {
        $this->authorize('delete', $sale);

        if ($sale->is_confirmed) {
            return back()->with('error', 'Impossible de supprimer une vente confirmée.');
        }

        $sale->delete();

        return redirect()
            ->route('sales.index')
            ->with('success', 'Vente supprimée avec succès.');
    }
}
