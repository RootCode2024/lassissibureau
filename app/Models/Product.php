<?php

namespace App\Models;

use App\Enums\ProductStatus;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'product_model_id',
        'imei',
        'serial_number',
        'status',
        'prix_achat',
        'prix_vente',
        'date_achat',
        'fournisseur',
        'notes',
        'condition',
        'defauts',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => ProductStatus::class,
        'prix_achat' => 'decimal:2',
        'prix_vente' => 'decimal:2',
        'date_achat' => 'date',
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
     * Modèle de produit
     */
    public function productModel(): BelongsTo
    {
        return $this->belongsTo(ProductModel::class);
    }

    /**
     * Mouvements de stock liés
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class)->orderByDesc('created_at');
    }

    /**
     * Dernier mouvement de stock
     */
    public function lastMovement(): HasOne
    {
        return $this->hasOne(StockMovement::class)->latestOfMany();
    }

    /**
     * Vente associée (si vendu)
     */
    public function sale(): HasOne
    {
        return $this->hasOne(Sale::class);
    }

    /**
     * Trade-in associé (si reçu en troc)
     */
    public function tradeIn(): HasOne
    {
        return $this->hasOne(TradeIn::class, 'product_received_id');
    }

    /**
     * Retour client associé
     */
    public function customerReturn(): HasOne
    {
        return $this->hasOne(CustomerReturn::class, 'returned_product_id');
    }

    /**
     * Utilisateur ayant créé le produit
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Utilisateur ayant modifié le produit
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Calculer le bénéfice potentiel
     */
    public function getBeneficePotentielAttribute(): float
    {
        return (float) ($this->prix_vente - $this->prix_achat);
    }

    /**
     * Calculer le taux de marge
     */
    public function getMargePercentageAttribute(): float
    {
        if ($this->prix_achat == 0) {
            return 0;
        }
        return round(($this->benefice_potentiel / $this->prix_achat) * 100, 2);
    }

    /**
     * Vérifier si le produit est disponible à la vente
     */
    public function isAvailable(): bool
    {
        return $this->status->isAvailable();
    }

    /**
     * Vérifier si le produit est en stock physique
     */
    public function isInStock(): bool
    {
        return $this->status->isInStock();
    }

    /**
     * Scope pour les produits en stock
     */
    public function scopeInStock($query)
    {
        return $query->whereIn('status', [
            ProductStatus::STOCK_BOUTIQUE->value,
            ProductStatus::REPARE->value,
        ]);
    }

    /**
     * Scope pour les produits vendus
     */
    public function scopeSold($query)
    {
        return $query->where('status', ProductStatus::VENDU->value);
    }

    /**
     * Scope pour les produits chez revendeurs
     */
    public function scopeChezRevendeur($query)
    {
        return $query->where('status', ProductStatus::CHEZ_REVENDEUR->value);
    }

    /**
     * Scope pour les produits à réparer
     */
    public function scopeAReparer($query)
    {
        return $query->where('status', ProductStatus::A_REPARER->value);
    }

    /**
     * Scope pour recherche par IMEI
     */
    public function scopeByImei($query, string $imei)
    {
        return $query->where('imei', $imei);
    }

    /**
     * Changer le statut et créer un mouvement de stock
     */
    public function changeStatus(
        ProductStatus $newStatus,
        string $movementType,
        ?int $userId = null,
        ?array $additionalData = []
    ): void {
        $oldStatus = $this->status;

        $this->update([
            'status' => $newStatus,
            'updated_by' => $userId ?? Auth::id(),
        ]);

        // Créer le mouvement de stock
        $this->stockMovements()->create(array_merge([
            'type' => $movementType,
            'status_before' => $oldStatus->value,
            'status_after' => $newStatus->value,
            'user_id' => $userId ?? Auth::id(),
        ], $additionalData));
    }
}
