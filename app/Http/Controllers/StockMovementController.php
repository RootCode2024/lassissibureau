<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStockMovementRequest;
use App\Models\Product;
use App\Models\StockMovement;
use App\Services\StockService;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    public function __construct(
        private StockService $stockService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = StockMovement::with(['product.productModel', 'user', 'sale', 'reseller']);

        // Filtres
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $movements = $query->latest()->paginate(50);

        return view('stock-movements.index', compact('movements'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStockMovementRequest $request)
    {
        $movement = $this->stockService->createMovement($request->validated());

        return redirect()
            ->route('stock-movements.show', $movement)
            ->with('success', 'Mouvement de stock enregistré avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(StockMovement $stockMovement)
    {
        $stockMovement->load(['product.productModel', 'user', 'sale', 'reseller', 'relatedProduct']);

        return view('stock-movements.show', compact('stockMovement'));
    }

    /**
     * Show form for creating a reception movement.
     */
    public function createReception()
    {
        $this->authorize('create', StockMovement::class);

        $products = Product::with('productModel')->get();

        return view('stock-movements.create-reception', compact('products'));
    }

    /**
     * Show form for creating an adjustment movement.
     */
    public function createAdjustment()
    {
        $this->authorize('create', StockMovement::class);

        $products = Product::with('productModel')->get();

        return view('stock-movements.create-adjustment', compact('products'));
    }
}
