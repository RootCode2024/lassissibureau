<?php

namespace App\Imports;

use App\Enums\ProductLocation;
use App\Enums\ProductState;
use App\Enums\StockMovementType;
use App\Models\Product;
use App\Models\ProductModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductsImport implements ToCollection, WithHeadingRow, WithValidation
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // 1. Trouver ou Créer le modèle
            $brand = Str::trim($row['marque']);
            $modelName = Str::trim($row['modele']); // 'modele' car 'model' est réservé parfois

            $productModel = ProductModel::firstOrCreate(
                [
                    'brand' => $brand, 
                    'name' => $modelName
                ],
                ['category' => 'telephone'] // Defaut si création à la volée
            );

            // 2. Mapping des Enums
            $state = $this->mapState($row['etat']);
            $location = $this->mapLocation($row['localisation']);

            // 3. Création du produit
            $product = Product::create([
                'product_model_id' => $productModel->id,
                'imei' => $row['imei'] ? Str::replaceMatches('/[^0-9]/', '', $row['imei']) : null,
                'serial_number' => $row['numero_serie'] ?? null,
                'state' => $state,
                'location' => $location,
                'prix_achat' => $this->parsePrice($row['prix_achat'] ?? 0),
                'prix_vente' => $this->parsePrice($row['prix_vente'] ?? 0),
                'fournisseur' => $row['fournisseur'] ?? null,
                'notes' => $row['notes'] ?? 'Import Excel',
                'condition' => $row['condition'] ?? 'Bon',
                'created_by' => Auth::id() ?? 1, // Fallback ID 1 si CLI
            ]);

            // 4. Mouvement de stock initial
            $product->stockMovements()->create([
                'type' => StockMovementType::RECEPTION_FOURNISSEUR->value,
                'quantity' => 1,
                'state_after' => $state->value,
                'location_after' => $location->value,
                'user_id' => Auth::id() ?? 1,
                'notes' => 'Import Initial Excel',
            ]);
        }
    }

    private function mapState($value)
    {
        $v = Str::slug(trim($value)); //Slugifie pour gérer accents et majuscules
        
        // Match avec des slugs
        return match(true) {
            Str::contains($v, ['neuf', 'new', 'nouveau', 'scelle']) => ProductState::DISPONIBLE,
            Str::contains($v, ['vendu', 'sold', 'sale', 'client']) => ProductState::VENDU,
            Str::contains($v, ['panne', 'hs', 'reparer', 'broken', 'casse']) => ProductState::A_REPARER,
            Str::contains($v, ['perdu', 'lost', 'vol']) => ProductState::PERDU,
            Str::contains($v, ['repare', 'fixed', 'ok']) => ProductState::REPARE,
            Str::contains($v, ['retour', 'return']) => ProductState::RETOUR_FOURNISSEUR,
            default => ProductState::DISPONIBLE,
        };
    }

    private function mapLocation($value)
    {
        $v = Str::slug(trim($value));
        
        return match(true) {
            Str::contains($v, ['boutique', 'magasin', 'shop', 'stock', 'agence']) => ProductLocation::BOUTIQUE,
            Str::contains($v, ['repar', 'atelier', 'sav']) => ProductLocation::EN_REPARATION,
            Str::contains($v, ['client', 'customer', 'vendu']) => ProductLocation::CHEZ_CLIENT,
            Str::contains($v, ['revendeur', 'partenaire', 'depot']) => ProductLocation::CHEZ_REVENDEUR,
            default => ProductLocation::BOUTIQUE,
        };
    }

    private function parsePrice($value)
    {
        // Enlève les espaces, les devises, et remplace virgule par point
        if (is_numeric($value)) return $value;
        $cleaned = preg_replace('/[^0-9,.]/', '', $value);
        $cleaned = str_replace(',', '.', $cleaned);
        return (float) $cleaned;
    }

    public function rules(): array
    {
        return [
            'marque' => ['required'],
            'modele' => ['required'],
            'imei' => ['nullable', 'unique:products,imei'], 
            'prix_achat' => ['nullable'],
            'prix_vente' => ['nullable'],
        ];
    }
    
    public function customValidationMessages()
    {
        return [
            'marque.required' => 'La colonne "marque" est vide ou manquante à la ligne :row.',
            'modele.required' => 'La colonne "modele" est vide ou manquante à la ligne :row.',
            'imei.unique' => 'L\'IMEI ":input" (ligne :row) existe déjà dans le système.',
            'imei.required_if' => 'L\'IMEI est obligatoire pour les téléphones (ligne :row).',
        ];
    }
}
