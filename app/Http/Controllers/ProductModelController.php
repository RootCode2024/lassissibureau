<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductModelRequest;
use App\Http\Requests\UpdateProductModelRequest;
use App\Models\ProductModel;
use Illuminate\Http\Request;

class ProductModelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', ProductModel::class);

        $query = ProductModel::query()->withCount('products', 'productsInStock');

        // Filtres
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'ILIKE', "%{$request->search}%")
                    ->orWhere('brand', 'ILIKE', "%{$request->search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->boolean('low_stock')) {
            $query->whereHas('productsInStock', function ($q) {
                $q->havingRaw('COUNT(*) <= product_models.stock_minimum');
            });
        }

        $productModels = $query->latest()->paginate(20);

        return view('product-models.index', compact('productModels'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', ProductModel::class);

        return view('product-models.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductModelRequest $request)
    {
        $productModel = ProductModel::create($request->validated());

        return redirect()
            ->route('product-models.show', $productModel)
            ->with('success', 'Modèle de produit créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductModel $productModel)
    {
        $this->authorize('view', $productModel);

        $productModel->load(['products' => function ($query) {
            $query->latest()->with('stockMovements')->take(20);
        }]);

        $stats = [
            'total_stock' => $productModel->stock_quantity,
            'total_sold' => $productModel->productsSold()->count(),
            'average_price' => $productModel->products()->avg('prix_vente'),
        ];

        return view('product-models.show', compact('productModel', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductModel $productModel)
    {
        $this->authorize('update', $productModel);

        return view('product-models.edit', compact('productModel'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductModelRequest $request, ProductModel $productModel)
    {
        $productModel->update($request->validated());

        return redirect()
            ->route('product-models.show', $productModel)
            ->with('success', 'Modèle de produit mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductModel $productModel)
    {
        $this->authorize('delete', $productModel);

        if ($productModel->products()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer ce modèle car des produits l\'utilisent.');
        }

        $productModel->delete();

        return redirect()
            ->route('product-models.index')
            ->with('success', 'Modèle de produit supprimé avec succès.');
    }
}
