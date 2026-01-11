<?php

namespace App\Models;

use App\Enums\StockMovementType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class StockMovement extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'product_id',
        'type',
        'quantity',
        'status_before',
        'status_after',
        'sale_id',
        'reseller_id',
        'related_product_id',
        'justification',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'type' => StockMovementType::class,
        'quantity' => 'integer',
    ];

    /**
     * Configuration de l'audit log
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Produit concerné
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Vente associée (si applicable)
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Revendeur associé (si applicable)
     */
    public function reseller(): BelongsTo
    {
        return $this->belongsTo(Reseller::class);
    }

    /**
     * Produit lié (pour échanges, etc.)
     */
    public function relatedProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'related_product_id');
    }

    /**
     * Utilisateur ayant effectué le mouvement
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Vérifier si c'est une entrée de stock
     */
    public function isIncrement(): bool
    {
        return $this->type->isIncrement();
    }

    /**
     * Vérifier si c'est une sortie de stock
     */
    public function isDecrement(): bool
    {
        return $this->type->isDecrement();
    }

    /**
     * Scope pour les entrées
     */
    public function scopeIncrements($query)
    {
        return $query->whereIn('type', array_map(
            fn($type) => $type->value,
            array_filter(
                StockMovementType::cases(),
                fn($type) => $type->isIncrement()
            )
        ));
    }

    /**
     * Scope pour les sorties
     */
    public function scopeDecrements($query)
    {
        return $query->whereIn('type', array_map(
            fn($type) => $type->value,
            array_filter(
                StockMovementType::cases(),
                fn($type) => $type->isDecrement()
            )
        ));
    }

    /**
     * Scope pour un type spécifique
     */
    public function scopeByType($query, StockMovementType $type)
    {
        return $query->where('type', $type->value);
    }

    /**
     * Scope pour une période
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope pour un produit spécifique
     */
    public function scopeForProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Scope pour un utilisateur spécifique
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope pour aujourd'hui
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope pour cette semaine
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek(),
        ]);
    }

    /**
     * Scope pour ce mois
     */
    public function scopeThisMonth($query)
    {
        return $query->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month);
    }
}
