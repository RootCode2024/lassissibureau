<?php

namespace App\Livewire;

use App\Models\ProductModel;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\WithPagination;

class ProductModelsTable extends Component
{
    use WithPagination;

    public $search = '';

    public $category = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'category' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategory()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'category']);
        $this->resetPage();
    }

    public function render()
    {
        // Query de base avec filtres
        $baseQuery = ProductModel::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'ILIKE', '%'.$this->search.'%')
                        ->orWhere('brand', 'ILIKE', '%'.$this->search.'%');
                });
            })
            ->when($this->category, function ($query) {
                $query->where('category', $this->category);
            });

        // 📊 Statistiques avec cache (5 minutes)
        $cacheKey = 'product_models_stats_'.md5($this->search.$this->category);

        $stats = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($baseQuery) {
            return [
                'total' => (clone $baseQuery)->count(),
                'actifs' => (clone $baseQuery)->where('is_active', true)->count(),
                'stock_faible' => (clone $baseQuery)
                    ->withCount('productsInStock')
                    ->get()
                    ->filter(function ($model) {
                        return $model->products_in_stock_count < $model->stock_minimum;
                    })
                    ->count(),
            ];
        });

        // 📋 Résultats paginés SEULEMENT
        $productModels = (clone $baseQuery)
            ->withCount([
                'products as products_count',
                'products as products_in_stock_count' => function ($query) {
                    $query->whereIn('location', ['boutique', 'en_reparation']);
                },
            ])
            ->latest()
            ->paginate(20);

        return view('livewire.product-models-table', [
            'productModels' => $productModels,
            'stats' => $stats,
        ]);
    }
}
