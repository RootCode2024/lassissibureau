<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Reseller extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'phone_secondary',
        'address',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Configuration de l'audit log
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'phone', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Ventes via ce revendeur
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Ventes confirmées
     */
    public function confirmedSales(): HasMany
    {
        return $this->sales()->where('is_confirmed', true);
    }

    /**
     * Ventes en attente (produits chez le revendeur)
     */
    public function pendingSales(): HasMany
    {
        return $this->sales()->where('is_confirmed', false);
    }

    /**
     * Mouvements de stock liés
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Calculer le total des ventes confirmées
     */
    public function getTotalSalesAttribute(): float
    {
        return (float) $this->confirmedSales()->sum('prix_vente');
    }

    /**
     * Calculer le total des bénéfices générés
     */
    public function getTotalBeneficeAttribute(): float
    {
        return (float) $this->confirmedSales()
            ->get()
            ->sum(fn ($sale) => $sale->benefice);
    }

    /**
     * Nombre de ventes confirmées
     */
    public function getNombreVentesAttribute(): int
    {
        return $this->confirmedSales()->count();
    }

    /**
     * Nombre de produits actuellement chez le revendeur
     */
    public function getProduitsEnCoursAttribute(): int
    {
        return $this->pendingSales()->count();
    }

    /**
     * Vérifier si le revendeur a des produits en attente
     */
    public function hasPendingProducts(): bool
    {
        return $this->produits_en_cours > 0;
    }

    /**
     * Scope pour les revendeurs actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour les revendeurs avec ventes en attente
     */
    public function scopeWithPendingSales($query)
    {
        return $query->whereHas('pendingSales');
    }

    /**
     * Scope pour recherche par téléphone
     */
    public function scopeByPhone($query, string $phone)
    {
        return $query->where('phone', 'LIKE', "%{$phone}%")
            ->orWhere('phone_secondary', 'LIKE', "%{$phone}%");
    }

    /**
     * Obtenir les ventes d'une période
     */
    public function salesBetweenDates($startDate, $endDate)
    {
        return $this->confirmedSales()
            ->whereBetween('date_vente_effective', [$startDate, $endDate])
            ->get();
    }

    /**
     * Obtenir le chiffre d'affaires d'une période
     */
    public function salesAmountBetweenDates($startDate, $endDate): float
    {
        return (float) $this->confirmedSales()
            ->whereBetween('date_vente_effective', [$startDate, $endDate])
            ->sum('prix_vente');
    }

    /**
     * Obtenir les bénéfices d'une période
     */
    public function beneficeBetweenDates($startDate, $endDate): float
    {
        return (float) $this->confirmedSales()
            ->whereBetween('date_vente_effective', [$startDate, $endDate])
            ->get()
            ->sum(fn ($sale) => $sale->benefice);
    }
}
