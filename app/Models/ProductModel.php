<?php

namespace App\Models;

use App\Enums\ProductLocation;
use App\Enums\ProductCategory;
use App\Enums\ProductState;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ProductModel extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'name',
        'brand',
        'description',
        'category',
        'image_url',
        'prix_revient_default',
        'prix_vente_default',
        'stock_minimum',
        'is_active',
    ];

    protected $casts = [
        'category' => ProductCategory::class,
        'prix_revient_default' => 'decimal:2',
        'prix_vente_default' => 'decimal:2',
        'stock_minimum' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Configuration de l'audit log
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'brand', 'prix_revient_default', 'prix_vente_default', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Produits individuels de ce modèle
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Produits actuellement en stock (en boutique ou en réparation)
     */
    public function productsInStock(): HasMany
    {
        return $this->products()
            ->whereIn('location', [
                ProductLocation::BOUTIQUE->value,
                ProductLocation::EN_REPARATION->value,
            ]);
    }

    /**
     * Produits disponibles à la vente
     */
    public function productsAvailableForSale(): HasMany
    {
        return $this->products()
            ->whereIn('state', [
                ProductState::DISPONIBLE->value,
                ProductState::REPARE->value,
            ])
            ->where('location', ProductLocation::BOUTIQUE->value);
    }

    /**
     * Produits vendus
     */
    public function productsSold(): HasMany
    {
        return $this->products()
            ->where('state', ProductState::VENDU->value);
    }

    /**
     * Produits chez les revendeurs
     */
    public function productsAtResellers(): HasMany
    {
        return $this->products()
            ->where('location', ProductLocation::CHEZ_REVENDEUR->value);
    }

    /**
     * Produits à réparer ou en réparation
     */
    public function productsInRepair(): HasMany
    {
        return $this->products()
            ->where(function ($query) {
                $query->where('state', ProductState::A_REPARER->value)
                    ->orWhere('location', ProductLocation::EN_REPARATION->value);
            });
    }

    /**
     * Produits chez les clients (vendus et livrés)
     */
    public function productsAtClients(): HasMany
    {
        return $this->products()
            ->where('location', ProductLocation::CHEZ_CLIENT->value);
    }

    /**
     * Quantité totale en stock (boutique + en réparation)
     */
    public function getStockQuantityAttribute(): int
    {
        return $this->productsInStock()->count();
    }

    /**
     * Quantité disponible à la vente
     */
    public function getAvailableQuantityAttribute(): int
    {
        return $this->productsAvailableForSale()->count();
    }

    /**
     * Quantité vendue
     */
    public function getSoldQuantityAttribute(): int
    {
        return $this->productsSold()->count();
    }

    /**
     * Quantité chez les revendeurs
     */
    public function getResellerQuantityAttribute(): int
    {
        return $this->productsAtResellers()->count();
    }

    /**
     * Quantité en réparation
     */
    public function getRepairQuantityAttribute(): int
    {
        return $this->productsInRepair()->count();
    }

    /**
     * Vérifie si le stock est en dessous du minimum
     */
    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->stock_minimum;
    }

    /**
     * Vérifie si le modèle a des produits disponibles
     */
    public function hasAvailableProducts(): bool
    {
        return $this->available_quantity > 0;
    }

    /**
     * Valeur totale du stock (prix d'achat)
     */
    public function getStockValueAttribute(): float
    {
        return (float) $this->productsInStock()->sum('prix_achat');
    }

    /**
     * Valeur potentielle de vente du stock
     */
    public function getStockSaleValueAttribute(): float
    {
        return (float) $this->productsInStock()->sum('prix_vente');
    }

    /**
     * Bénéfice potentiel du stock
     */
    public function getStockPotentialProfitAttribute(): float
    {
        return $this->stock_sale_value - $this->stock_value;
    }

    /**
     * Scope pour les modèles actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour les téléphones uniquement
     */
    public function scopeTelephones($query)
    {
        return $query->where('category', 'telephone');
    }

    /**
     * Scope pour les accessoires uniquement
     */
    public function scopeAccessoires($query)
    {
        return $query->where('category', 'accessoire');
    }

    /**
     * Scope pour les modèles en stock bas
     */
    public function scopeLowStock($query)
    {
        return $query->whereHas('productsInStock', function ($q) {
            // Ne compte que les produits en stock
        }, '<=', function ($query) {
            return $query->select('stock_minimum');
        });
    }

    /**
     * Scope avec statistiques de stock
     */
    public function scopeWithStockStats($query)
    {
        return $query->withCount([
            'products',
            'productsInStock',
            'productsAvailableForSale',
            'productsSold',
            'productsAtResellers',
            'productsInRepair',
        ]);
    }

    /**
     * Obtenir un résumé complet du stock
     */
    public function getStockSummary(): array
    {
        return [
            'total_products' => $this->products()->count(),
            'in_stock' => $this->stock_quantity,
            'available_for_sale' => $this->available_quantity,
            'sold' => $this->sold_quantity,
            'at_resellers' => $this->reseller_quantity,
            'in_repair' => $this->repair_quantity,
            'stock_value' => $this->stock_value,
            'potential_sale_value' => $this->stock_sale_value,
            'potential_profit' => $this->stock_potential_profit,
            'is_low_stock' => $this->isLowStock(),
            'minimum_stock' => $this->stock_minimum,
        ];
    }
}
