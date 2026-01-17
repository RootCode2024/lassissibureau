<?php

namespace Database\Factories;

use App\Enums\ProductLocation;
use App\Enums\ProductState;
use App\Models\Product;
use App\Models\ProductModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_model_id' => ProductModel::factory(),
            'imei' => $this->faker->unique()->numerify('###############'),
            'serial_number' => $this->faker->unique()->bothify('??##########'),
            'state' => ProductState::DISPONIBLE,
            'location' => ProductLocation::BOUTIQUE,
            'prix_achat' => $this->faker->randomFloat(2, 50000, 200000),
            'prix_vente' => $this->faker->randomFloat(2, 210000, 400000),
            'date_achat' => $this->faker->date(),
            'fournisseur' => $this->faker->company(),
            'notes' => $this->faker->sentence(),
            'condition' => 'Comme neuf',
            'created_by' => User::factory(),
            'updated_by' => User::factory(),
        ];
    }
}
