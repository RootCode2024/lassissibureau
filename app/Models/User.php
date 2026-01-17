<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasRoles, LogsActivity, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Configuration de l'audit log
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Produits créés par cet utilisateur
     */
    public function productsCreated(): HasMany
    {
        return $this->hasMany(Product::class, 'created_by');
    }

    /**
     * Produits modifiés par cet utilisateur
     */
    public function productsUpdated(): HasMany
    {
        return $this->hasMany(Product::class, 'updated_by');
    }

    /**
     * Ventes effectuées par cet utilisateur
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class, 'sold_by');
    }

    /**
     * Mouvements de stock effectués par cet utilisateur
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'user_id');
    }

    /**
     * Retours clients traités par cet utilisateur
     */
    public function customerReturnsProcessed(): HasMany
    {
        return $this->hasMany(CustomerReturn::class, 'processed_by');
    }

    /**
     * Vérifier si l'utilisateur est admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(UserRole::ADMIN->value);
    }

    /**
     * Vérifier si l'utilisateur est vendeur
     */
    public function isVendeur(): bool
    {
        return $this->hasRole(UserRole::VENDEUR->value);
    }

    /**
     * Obtenir le rôle principal de l'utilisateur
     */
    public function getPrimaryRoleAttribute(): ?string
    {
        return $this->roles->first()?->name;
    }

    /**
     * Scope pour les admins uniquement
     */
    public function scopeAdmins(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->role(UserRole::ADMIN->value);
    }

    /**
     * Scope pour les vendeurs uniquement
     */
    public function scopeVendeurs(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->role(UserRole::VENDEUR->value);
    }

    /**
     * Statistiques de vente pour cet utilisateur
     */
    public function salesStats($startDate = null, $endDate = null)
    {
        $query = $this->sales()->confirmed();

        if ($startDate && $endDate) {
            $query->whereBetween('date_vente_effective', [$startDate, $endDate]);
        }

        $sales = $query->get();

        return [
            'total_ventes' => $sales->count(),
            'chiffre_affaires' => $sales->sum('prix_vente'),
            'benefice_total' => $sales->sum(fn ($sale) => $sale->benefice),
        ];
    }
}
