<?php

namespace App\Imports;

use App\Enums\ProductCategory;
use App\Models\ProductModel;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Str;

class ProductModelsImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // Nettoyage et normalisation
        $brand = Str::ucfirst(Str::lower(trim($row['marque'])));
        $name = trim($row['modele']);
        
        // Mapping de la catégorie
        $categoryInput = Str::lower(trim($row['categorie']));
        $category = match($categoryInput) {
            'telephone', 'téléphone', 'phone', 'smartphone' => ProductCategory::TELEPHONE,
            'tablette', 'tablet', 'ipad' => ProductCategory::TABLETTE,
            'accessoire', 'accessory', 'accessoires' => ProductCategory::ACCESSOIRE,
            default => ProductCategory::TELEPHONE,
        };

        return ProductModel::updateOrCreate(
            [
                'brand' => $brand,
                'name' => $name,
            ],
            [
                'category' => $category,
                'prix_vente_default' => $row['prix_vente_defaut'] ?? 0,
                'prix_revient_default' => $row['prix_achat_defaut'] ?? 0,
                'stock_minimum' => $row['seuil_alerte'] ?? 5,
            ]
        );
    }

    public function rules(): array
    {
        return [
            'marque' => ['required', 'max:50'],
            'modele' => ['required', 'max:100'],
            'categorie' => ['required'],
            'prix_vente_defaut' => ['nullable', 'numeric', 'min:0'],
            'prix_achat_defaut' => ['nullable', 'numeric', 'min:0'],
            'seuil_alerte' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            'marque.required' => 'La colonne "marque" est vide ou manquante à la ligne :row.',
            'marque.max' => 'La marque est trop longue (max 50 chars) à la ligne :row.',
            'modele.required' => 'La colonne "modele" est vide ou manquante à la ligne :row.',
            'categorie.required' => 'La colonne "categorie" est vide ou manquante à la ligne :row.',
            'prix_vente_defaut.numeric' => 'Le prix de vente (ligne :row) doit être un nombre.',
            'prix_achat_defaut.numeric' => 'Le prix d\'achat (ligne :row) doit être un nombre.',
            'seuil_alerte.integer' => 'Le seuil d\'alerte (ligne :row) doit être un nombre entier.',
        ];
    }
}
