<?php

namespace App\Livewire;

use App\Models\ProductModel;
use Livewire\Component;
use Livewire\WithPagination;

class ProductModelsTable extends Component
{
    use WithPagination;

    public $search = '';
    public $category = '';

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
        // Requête de base
        $query = ProductModel::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    // ✅ ILIKE pour PostgreSQL (insensible à la casse)
                    $q->where('name', 'ILIKE', '%' . $this->search . '%')
                        ->orWhere('brand', 'ILIKE', '%' . $this->search . '%');
                });
            })
            ->when($this->category, function ($query) {
                $query->where('category', $this->category);
            })
            ->withCount(['products as products_in_stock_count' => function ($query) {
                $query->whereIn('location', ['boutique', 'en_reparation']);
            }]);

        // Calculer les statistiques sur TOUS les modèles (avec filtres appliqués)
        $allModelsQuery = clone $query;
        $allModels = $allModelsQuery->get();

        $stats = [
            'total' => $allModels->count(),
            'actifs' => $allModels->where('is_active', true)->count(),
            'stock_faible' => $allModels->filter(function ($model) {
                return $model->products_in_stock_count < $model->stock_minimum;
            })->count(),
        ];

        // Paginer les résultats pour l'affichage
        $productModels = $query->latest()->paginate(10);

        return view('livewire.product-models-table', [
            'productModels' => $productModels,
            'stats' => $stats,
        ]);
    }
}
