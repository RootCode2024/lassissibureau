<?php

namespace App\Imports;

use App\Models\Reseller;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Str;

class ResellersImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return new Reseller([
            'name' => trim($row['nom']),
            'phone' => $this->cleanPhone($row['telephone_1']),
            'phone_secondary' => isset($row['telephone_2']) ? $this->cleanPhone($row['telephone_2']) : null,
            'address' => isset($row['adresse']) ? trim($row['adresse']) : null,
            'is_active' => true,
        ]);
    }

    private function cleanPhone($phone)
    {
        if (empty($phone)) return null;
        // Enlève tout sauf chiffres et +
        return Str::replaceMatches('/[^0-9+]/', '', $phone);
    }

    public function rules(): array
    {
        return [
            'nom' => ['required', 'max:255'],
            'telephone_1' => ['required', 'unique:resellers,phone'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            'telephone_1.unique' => 'Le numéro de téléphone :input (ligne :row) est déjà utilisé par un autre revendeur.',
            'telephone_1.required' => 'Le numéro de téléphone principal est manquant à la ligne :row.',
            'nom.required' => 'Le nom du revendeur est manquant à la ligne :row.',
            'nom.max' => 'Le nom du revendeur est trop long (max 255 caractères) à la ligne :row.',
        ];
    }
}
