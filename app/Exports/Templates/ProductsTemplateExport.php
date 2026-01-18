<?php

namespace App\Exports\Templates;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductsTemplateExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    public function array(): array
    {
        return [
            ['Samsung', 'Galaxy S24', '356789012345678', 'Neuf', 'Boutique', 600000, 750000, 'Fournisseur A', 'Arrivage du jour', 'Neuf scellé'],
            ['Apple', 'iPhone 13', '359876543210987', 'Occasion', 'Stock', 250000, 300000, 'Reprise Client', 'Rayure écran', 'Bon état'],
        ];
    }

    public function headings(): array
    {
        return [
            'marque',
            'modele',
            'imei',
            'etat',
            'localisation',
            'prix_achat',
            'prix_vente',
            'fournisseur',
            'notes',
            'condition',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
