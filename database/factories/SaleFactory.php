<?php

namespace Database\Factories;

use App\Models\Sale;
use App\Models\Product;
use App\Models\User;
use App\Enums\SaleType;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sale>
 */
class SaleFactory extends Factory
{
    protected $model = Sale::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $prixVente = $this->faker->randomFloat(2, 200000, 400000);
        return [
            'product_id' => Product::factory(),
            'sale_type' => SaleType::ACHAT_DIRECT,
            'prix_vente' => $prixVente,
            'prix_achat_produit' => $this->faker->randomFloat(2, 100000, 190000),
            'date_vente_effective' => now(),
            'is_confirmed' => true,
            'payment_status' => PaymentStatus::PAID,
            'amount_paid' => $prixVente,
            'amount_remaining' => 0,
            'sold_by' => User::factory(),
        ];
    }
}
