<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ProductModel extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

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
     * Produits actuellement en stock
     */
    public function productsInStock(): HasMany
    {
        return $this->products()
            ->whereIn('status', [
                \App\Enums\ProductStatus::STOCK_BOUTIQUE->value,
                \App\Enums\ProductStatus::REPARE->value,
            ]);
    }

    /**
     * Produits vendus
     */
    public function productsSold(): HasMany
    {
        return $this->products()
            ->where('status', \App\Enums\ProductStatus::VENDU->value);
    }

    /**
     * Quantité totale en stock
     */
    public function getStockQuantityAttribute(): int
    {
        return $this->productsInStock()->count();
    }

    /**
     * Vérifie si le stock est en dessous du minimum
     */
    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->stock_minimum;
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
}
