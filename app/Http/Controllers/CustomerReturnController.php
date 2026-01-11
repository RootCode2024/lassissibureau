<?php

namespace App\Http\Controllers;

use App\Enums\ProductStatus;
use App\Enums\StockMovementType;
use App\Http\Requests\StoreCustomerReturnRequest;
use App\Models\CustomerReturn;
use App\Models\Product;
use App\Models\Sale;
use App\Services\ProductService;
use App\Services\SaleService;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerReturnController extends Controller
{
    public function __construct(
        private ProductService $productService,
        private SaleService $saleService,
        private StockService $stockService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', CustomerReturn::class);

        $query = CustomerReturn::with(['originalSale.product.productModel', 'returnedProduct', 'exchangeProduct', 'processor']);

        // Filtres
        if ($request->filled('is_exchange')) {
            $query->where('is_exchange', $request->boolean('is_exchange'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $returns = $query->latest()->paginate(20);

        return view('returns.index', compact('returns'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->authorize('create', CustomerReturn::class);

        // Vente présélectionnée
        $sale = null;
        if ($request->filled('sale_id')) {
            $sale = Sale::with('product.productModel')->find($request->sale_id);
        }

        // Ventes récentes confirmées
        $recentSales = Sale::with('product.productModel')
            ->confirmed()
            ->whereHas('product', function ($q) {
                $q->where('status', ProductStatus::VENDU);
            })
            ->latest()
            ->take(50)
            ->get();

        // Produits disponibles pour échange
        $availableProducts = Product::inStock()->with('productModel')->get();

        return view('returns.create', compact('sale', 'recentSales', 'availableProducts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerReturnRequest $request)
    {
        $customerReturn = DB::transaction(function () use ($request) {
            $validated = $request->validated();

            // Créer le retour client
            $customerReturn = CustomerReturn::create($validated);

            $returnedProduct = Product::findOrFail($validated['returned_product_id']);

            if ($validated['is_exchange']) {
                // C'est un échange
                $exchangeProduct = Product::findOrFail($validated['exchange_product_id']);

                // Créer une nouvelle vente pour le produit d'échange
                $newSale = $this->saleService->createSale([
                    'product_id' => $exchangeProduct->id,
                    'sale_type' => $customerReturn->originalSale->sale_type,
                    'prix_vente' => $exchangeProduct->prix_vente,
                    'prix_achat_produit' => $exchangeProduct->prix_achat,
                    'client_name' => $customerReturn->originalSale->client_name,
                    'client_phone' => $customerReturn->originalSale->client_phone,
                    'date_vente_effective' => now()->format('Y-m-d'),
                    'is_confirmed' => true,
                    'sold_by' => $validated['processed_by'],
                    'notes' => 'Échange suite retour - Retour original: #' . $customerReturn->original_sale_id,
                ]);

                $customerReturn->update(['exchange_sale_id' => $newSale->id]);

                // Mouvement pour le produit d'échange
                $this->stockService->createMovement([
                    'product_id' => $exchangeProduct->id,
                    'type' => StockMovementType::ECHANGE_RETOUR->value,
                    'quantity' => 1,
                    'status_after' => ProductStatus::VENDU->value,
                    'related_product_id' => $returnedProduct->id,
                    'user_id' => $validated['processed_by'],
                    'notes' => 'Échange suite retour client',
                ]);
            }

            // Mouvement pour le produit retourné
            $this->stockService->createMovement([
                'product_id' => $returnedProduct->id,
                'type' => StockMovementType::RETOUR_CLIENT->value,
                'quantity' => 1,
                'status_after' => ProductStatus::RETOUR_CLIENT->value,
                'user_id' => $validated['processed_by'],
                'notes' => 'Retour client - ' . $validated['reason'],
            ]);

            return $customerReturn->fresh(['originalSale', 'returnedProduct', 'exchangeProduct', 'exchangeSale']);
        });

        return redirect()
            ->route('returns.show', $customerReturn)
            ->with('success', 'Retour client enregistré avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CustomerReturn $customerReturn)
    {
        $this->authorize('view', $customerReturn);

        $customerReturn->load([
            'originalSale.product.productModel',
            'returnedProduct.productModel',
            'exchangeProduct.productModel',
            'exchangeSale',
            'processor'
        ]);

        return view('returns.show', compact('customerReturn'));
    }
}
