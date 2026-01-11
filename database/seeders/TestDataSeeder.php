<?php

namespace Database\Seeders;

use App\Enums\ProductStatus;
use App\Models\ProductModel;
use App\Models\Product;
use App\Models\Reseller;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔄 Création des données de test...');

        // Créer des modèles de produits
        $productModels = [
            [
                'name' => 'iPhone 13 Pro Max 256GB',
                'brand' => 'Apple',
                'category' => 'telephone',
                'prix_revient_default' => 500000,
                'prix_vente_default' => 650000,
                'stock_minimum' => 2,
            ],
            [
                'name' => 'iPhone 12 128GB',
                'brand' => 'Apple',
                'category' => 'telephone',
                'prix_revient_default' => 350000,
                'prix_vente_default' => 450000,
                'stock_minimum' => 3,
            ],
            [
                'name' => 'Samsung Galaxy S21 128GB',
                'brand' => 'Samsung',
                'category' => 'telephone',
                'prix_revient_default' => 300000,
                'prix_vente_default' => 400000,
                'stock_minimum' => 2,
            ],
            [
                'name' => 'iPhone 11 64GB',
                'brand' => 'Apple',
                'category' => 'telephone',
                'prix_revient_default' => 250000,
                'prix_vente_default' => 320000,
                'stock_minimum' => 3,
            ],
            [
                'name' => 'AirPods Pro 2',
                'brand' => 'Apple',
                'category' => 'accessoire',
                'prix_revient_default' => 80000,
                'prix_vente_default' => 110000,
                'stock_minimum' => 5,
            ],
            [
                'name' => 'Chargeur iPhone 20W',
                'brand' => 'Apple',
                'category' => 'accessoire',
                'prix_revient_default' => 5000,
                'prix_vente_default' => 8000,
                'stock_minimum' => 10,
            ],
        ];

        foreach ($productModels as $modelData) {
            $model = ProductModel::create($modelData);

            // Créer quelques produits pour chaque modèle
            if ($modelData['category'] === 'telephone') {
                // Créer 2-3 téléphones en stock pour chaque modèle
                for ($i = 1; $i <= rand(2, 3); $i++) {
                    Product::create([
                        'product_model_id' => $model->id,
                        'imei' => $this->generateFakeImei(),
                        'status' => ProductStatus::STOCK_BOUTIQUE,
                        'prix_achat' => $modelData['prix_revient_default'],
                        'prix_vente' => $modelData['prix_vente_default'],
                        'date_achat' => now()->subDays(rand(1, 30)),
                        'fournisseur' => 'Fournisseur ' . rand(1, 3),
                        'condition' => collect(['Neuf', 'Excellent', 'Bon'])->random(),
                        'created_by' => 1, // Admin
                    ]);
                }
            }
        }

        $this->command->info('✅ ' . ProductModel::count() . ' modèles de produits créés');
        $this->command->info('✅ ' . Product::count() . ' produits créés');

        // Créer quelques revendeurs
        $resellers = [
            [
                'name' => 'Jean Revendeur',
                'phone' => '+229 97 12 34 56',
                'address' => 'Cotonou, Akpakpa',
            ],
            [
                'name' => 'Marie Commerce',
                'phone' => '+229 96 78 90 12',
                'address' => 'Cotonou, Cadjèhoun',
            ],
            [
                'name' => 'Paul Distribution',
                'phone' => '+229 95 45 67 89',
                'address' => 'Porto-Novo',
            ],
        ];

        foreach ($resellers as $resellerData) {
            Reseller::create($resellerData);
        }

        $this->command->info('✅ ' . Reseller::count() . ' revendeurs créés');
        $this->command->info('');
        $this->command->info('🎉 Données de test créées avec succès!');
    }

    /**
     * Générer un faux IMEI (15 chiffres)
     */
    private function generateFakeImei(): string
    {
        return '35' . rand(1000000, 9999999) . rand(100000, 999999);
    }
}
