<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResellerRequest;
use App\Http\Requests\UpdateResellerRequest;
use App\Models\Reseller;
use Illuminate\Http\Request;

class ResellerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Reseller::class);

        $query = Reseller::withCount(['sales', 'confirmedSales', 'pendingSales']);

        // Filtres
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'ILIKE', "%{$request->search}%")
                    ->orWhere('phone', 'LIKE', "%{$request->search}%");
            });
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->boolean('with_pending')) {
            $query->withPendingSales();
        }

        $resellers = $query->latest()->paginate(20);

        return view('resellers.index', compact('resellers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Reseller::class);

        return view('resellers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreResellerRequest $request)
    {
        $reseller = Reseller::create($request->validated());

        return redirect()
            ->route('resellers.show', $reseller)
            ->with('success', 'Revendeur créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Reseller $reseller)
    {
        $this->authorize('view', $reseller);

        $reseller->load(['sales.product.productModel', 'confirmedSales', 'pendingSales']);

        $stats = [
            'total_sales' => $reseller->total_sales,
            'total_benefice' => $reseller->total_benefice,
            'nombre_ventes' => $reseller->nombre_ventes,
            'produits_en_cours' => $reseller->produits_en_cours,
        ];

        return view('resellers.show', compact('reseller', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reseller $reseller)
    {
        $this->authorize('update', $reseller);

        return view('resellers.edit', compact('reseller'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateResellerRequest $request, Reseller $reseller)
    {
        $reseller->update($request->validated());

        return redirect()
            ->route('resellers.show', $reseller)
            ->with('success', 'Revendeur mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reseller $reseller)
    {
        $this->authorize('delete', $reseller);

        if ($reseller->hasPendingProducts()) {
            return back()->with('error', 'Impossible de supprimer ce revendeur car il a des produits en cours.');
        }

        $reseller->delete();

        return redirect()
            ->route('resellers.index')
            ->with('success', 'Revendeur supprimé avec succès.');
    }

    /**
     * Display reseller statistics.
     */
    public function statistics(Request $request, Reseller $reseller)
    {
        $this->authorize('viewStatistics', $reseller);

        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        $stats = [
            'sales_amount' => $reseller->salesAmountBetweenDates($startDate, $endDate),
            'benefice' => $reseller->beneficeBetweenDates($startDate, $endDate),
            'sales' => $reseller->salesBetweenDates($startDate, $endDate),
        ];

        return view('resellers.statistics', compact('reseller', 'stats', 'startDate', 'endDate'));
    }
}
