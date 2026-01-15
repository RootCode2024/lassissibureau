<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use App\Enums\ProductState;
use App\Models\ProductModel;
use Livewire\WithPagination;
use App\Enums\ProductLocation;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Log;

class ProductsTable extends Component
{
    use WithPagination;

    // Étape actuelle (1 = sélection catégorie, 2 = liste produits)
    public $step = 1;
    public $selectedCategory = '';

    public $search = '';
    public $state = '';
    public $location = '';
    public $product_model_id = '';
    public $condition = '';

    protected $queryString = [
        'step' => ['except' => 1],
        'selectedCategory' => ['except' => ''],
        'search' => ['except' => ''],
        'state' => ['except' => ''],
        'location' => ['except' => ''],
        'product_model_id' => ['except' => ''],
        'condition' => ['except' => ''],
    ];

    public function selectCategory($category)
    {
        $this->selectedCategory = $category;
        $this->step = 2;
        $this->resetPage();
    }

    public function backToCategories()
    {
        $this->step = 1;
        $this->selectedCategory = '';
        $this->resetFilters();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingState()
    {
        $this->resetPage();
    }

    public function updatingLocation()
    {
        $this->resetPage();
    }

    public function updatingProductModelId()
    {
        $this->resetPage();
    }

    public function updatingCondition()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'state', 'location', 'product_model_id', 'condition']);
        $this->resetPage();
    }

    #[Computed]
    public function categories()
    {
        // Récupérer toutes les catégories distinctes depuis ProductModel
        $allCategories = ProductModel::whereNull('deleted_at')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->pluck('category')
            ->filter();

        // Log pour debugging
        Log::info('Categories found:', $allCategories->toArray());

        $categories = $allCategories->map(function ($category) {
            // Compter les modèles de cette catégorie
            $modelsCount = ProductModel::where('category', $category)
                ->whereNull('deleted_at')
                ->count();

            // Compter les produits de cette catégorie
            $productsCount = Product::whereHas('productModel', function ($q) use ($category) {
                $q->where('category', $category);
            })->whereNull('deleted_at')->count();

            Log::info("Category: {$category}, Models: {$modelsCount}, Products: {$productsCount}");

            return [
                'value' => $category,
                'label' => $this->getCategoryLabel($category),
                'icon' => $this->getCategoryIcon($category),
                'models_count' => $modelsCount,
                'products_count' => $productsCount,
            ];
        })
            ->filter(function ($cat) {
                // Ne garder que les catégories qui ont au moins un modèle
                return $cat['models_count'] > 0;
            })
            ->values();

        return $categories;
    }

    #[Computed]
    public function products()
    {
        if ($this->step !== 2 || !$this->selectedCategory) {
            return collect();
        }

        $query = Product::with(['productModel'])
            ->whereHas('productModel', function ($q) {
                $q->where('category', $this->selectedCategory);
            });

        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('imei', 'LIKE', '%' . $search . '%')
                    ->orWhere('serial_number', 'LIKE', '%' . $search . '%')
                    ->orWhereHas('productModel', function ($q) use ($search) {
                        $q->where('name', 'LIKE', '%' . $search . '%')
                            ->orWhere('brand', 'LIKE', '%' . $search . '%');
                    });
            });
        }

        if ($this->state !== '' && $this->state !== null) {
            $query->where('state', $this->state);
        }

        if ($this->location !== '' && $this->location !== null) {
            $query->where('location', $this->location);
        }

        if ($this->product_model_id !== '' && $this->product_model_id !== null) {
            $query->where('product_model_id', $this->product_model_id);
        }

        if ($this->condition !== '' && $this->condition !== null) {
            $query->where('condition', $this->condition);
        }

        return $query->latest()->paginate(15);
    }

    #[Computed]
    public function stats()
    {
        if ($this->step !== 2 || !$this->selectedCategory) {
            return [
                'total' => 0,
                'available' => 0,
                'chez_revendeur' => 0,
                'a_reparer' => 0,
            ];
        }

        $baseQuery = Product::whereHas('productModel', function ($q) {
            $q->where('category', $this->selectedCategory);
        });

        return [
            'total' => (clone $baseQuery)->count(),
            'available' => (clone $baseQuery)
                ->where('state', ProductState::DISPONIBLE->value)
                ->where('location', ProductLocation::BOUTIQUE->value)
                ->count(),
            'chez_revendeur' => (clone $baseQuery)
                ->where('location', ProductLocation::CHEZ_REVENDEUR->value)
                ->count(),
            'a_reparer' => (clone $baseQuery)
                ->where('state', ProductState::A_REPARER->value)
                ->count(),
        ];
    }

    #[Computed]
    public function productModels()
    {
        if ($this->step !== 2 || !$this->selectedCategory) {
            return collect();
        }

        return ProductModel::where('is_active', true)
            ->where('category', $this->selectedCategory)
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function states()
    {
        return collect(ProductState::cases())->map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label()
        ])->toArray();
    }

    #[Computed]
    public function locations()
    {
        return collect(ProductLocation::cases())->map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label()
        ])->toArray();
    }

    #[Computed]
    public function conditions()
    {
        if ($this->step !== 2 || !$this->selectedCategory) {
            return [];
        }

        return Product::whereHas('productModel', function ($q) {
            $q->where('category', $this->selectedCategory);
        })
            ->whereNotNull('condition')
            ->where('condition', '!=', '')
            ->distinct()
            ->pluck('condition')
            ->toArray();
    }

    private function getCategoryLabel($category)
    {
        return match ($category) {
            'telephone' => 'Téléphones',
            'tablette' => 'Tablettes',
            'pc' => 'Ordinateurs',
            'accessoire' => 'Accessoires',
            default => ucfirst($category),
        };
    }

    private function getCategoryIcon($category)
    {
        return match ($category) {
            'telephone' => 'smartphone',
            'tablette' => 'tablet',
            'pc' => 'monitor',
            'accessoire' => 'box',
            default => 'box',
        };
    }

    public function render()
    {
        return view('livewire.products-table', [
            'categories' => $this->categories,
            'products' => $this->products,
            'stats' => $this->stats,
            'productModels' => $this->productModels,
            'states' => $this->states,
            'locations' => $this->locations,
            'conditions' => $this->conditions,
        ]);
    }
}
