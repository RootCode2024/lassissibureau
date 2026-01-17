<?php

namespace Database\Factories;

use App\Models\ProductModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductModel>
 */
class ProductModelFactory extends Factory
{
    protected $model = ProductModel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'brand' => $this->faker->company(),
            'description' => $this->faker->sentence(),
            'category' => $this->faker->randomElement(['telephone', 'tablette', 'pc', 'accessoire']),
            'prix_revient_default' => $this->faker->randomFloat(2, 50000, 200000),
            'prix_vente_default' => $this->faker->randomFloat(2, 210000, 400000),
            'stock_minimum' => $this->faker->numberBetween(1, 10),
            'is_active' => true,
        ];
    }
}
