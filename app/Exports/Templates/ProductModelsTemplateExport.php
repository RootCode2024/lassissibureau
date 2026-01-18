<?php

namespace App\Exports\Templates;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductModelsTemplateExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    public function array(): array
    {
        return [
            ['Samsung', 'Galaxy S24 Ultra', 'Telephone', 850000, 700000, 5],
            ['Apple', 'iPhone 13 Pro Max 256GB', 'Telephone', 600000, 500000, 5],
            ['Apple', 'iPad Pro 12.9 M2', 'Tablette', 750000, 650000, 3],
            ['Apple', 'AirPods Pro 2', 'Accessoire', 150000, 120000, 10],
        ];
    }

    public function headings(): array
    {
        return [
            'marque',
            'modele',
            'categorie',
            'prix_vente_defaut',
            'prix_achat_defaut',
            'seuil_alerte',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
        ];
    }
}
