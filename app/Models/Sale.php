<?php

namespace App\Models;

use App\Enums\SaleType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Sale extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'product_id',
        'sale_type',
        'prix_vente',
        'prix_achat_produit',
        'client_name',
        'client_phone',
        'reseller_id',
        'date_depot_revendeur',
        'date_confirmation_vente',
        'is_confirmed',
        'date_vente_effective',
        'sold_by',
        'notes',
    ];

    protected $casts = [
        'sale_type' => SaleType::class,
        'prix_vente' => 'decimal:2',
        'prix_achat_produit' => 'decimal:2',
        'date_depot_revendeur' => 'date',
        'date_confirmation_vente' => 'date',
        'date_vente_effective' => 'date',
        'is_confirmed' => 'boolean',
    ];

    /**
     * Configuration de l'audit log
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Produit vendu
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Revendeur (si vente via revendeur)
     */
    public function reseller(): BelongsTo
    {
        return $this->belongsTo(Reseller::class);
    }

    /**
     * Vendeur
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sold_by');
    }

    /**
     * Trade-in associé (si vente avec troc)
     */
    public function tradeIn(): HasOne
    {
        return $this->hasOne(TradeIn::class);
    }

    /**
     * Retour client associé
     */
    public function customerReturn(): HasOne
    {
        return $this->hasOne(CustomerReturn::class, 'original_sale_id');
    }

    /**
     * Calculer le bénéfice réel
     */
    public function getBeneficeAttribute(): float
    {
        return (float) ($this->prix_vente - $this->prix_achat_produit);
    }

    /**
     * Calculer le taux de marge
     */
    public function getMargePercentageAttribute(): float
    {
        if ($this->prix_achat_produit == 0) {
            return 0;
        }
        return round(($this->benefice / $this->prix_achat_produit) * 100, 2);
    }

    /**
     * Vérifier si c'est une vente avec troc
     */
    public function hasTradeIn(): bool
    {
        return in_array($this->sale_type, [
            SaleType::TROC,
            SaleType::TROC_AVEC_COMPLEMENT,
        ]);
    }

    /**
     * Vérifier si c'est une vente via revendeur
     */
    public function isResellerSale(): bool
    {
        return $this->reseller_id !== null;
    }

    /**
     * Confirmer la vente (pour les revendeurs)
     */
    public function confirm(?string $notes = null): void
    {
        $this->update([
            'is_confirmed' => true,
            'date_confirmation_vente' => now(),
            'notes' => $notes ? $this->notes . "\n" . $notes : $this->notes,
        ]);

        // Mettre à jour le statut du produit
        $this->product->update(['status' => \App\Enums\ProductStatus::VENDU]);
    }

    /**
     * Scope pour les ventes confirmées
     */
    public function scopeConfirmed($query)
    {
        return $query->where('is_confirmed', true);
    }

    /**
     * Scope pour les ventes en attente (chez revendeur)
     */
    public function scopePending($query)
    {
        return $query->where('is_confirmed', false);
    }

    /**
     * Scope pour les ventes d'une date spécifique
     */
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('date_vente_effective', $date);
    }

    /**
     * Scope pour les ventes d'une période
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('date_vente_effective', [$startDate, $endDate]);
    }

    /**
     * Scope pour les ventes du jour
     */
    public function scopeToday($query)
    {
        return $query->whereDate('date_vente_effective', today());
    }

    /**
     * Scope pour les ventes de la semaine
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('date_vente_effective', [
            now()->startOfWeek(),
            now()->endOfWeek(),
        ]);
    }

    /**
     * Scope pour les ventes du mois
     */
    public function scopeThisMonth($query)
    {
        return $query->whereYear('date_vente_effective', now()->year)
            ->whereMonth('date_vente_effective', now()->month);
    }

    /**
     * Scope pour les ventes par type
     */
    public function scopeByType($query, SaleType $type)
    {
        return $query->where('sale_type', $type->value);
    }

    /**
     * Scope pour les ventes directes uniquement
     */
    public function scopeDirectSales($query)
    {
        return $query->where('sale_type', SaleType::ACHAT_DIRECT->value);
    }

    /**
     * Scope pour les ventes avec troc
     */
    public function scopeWithTradeIn($query)
    {
        return $query->whereIn('sale_type', [
            SaleType::TROC->value,
            SaleType::TROC_AVEC_COMPLEMENT->value,
        ]);
    }
}
