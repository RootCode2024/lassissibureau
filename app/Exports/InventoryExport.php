<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventoryExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function query()
    {
        return Product::query()
            ->with(['productModel'])
            ->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'Date Ajout',
            'Marque',
            'Modèle',
            'IMEI',
            'Numéro Série',
            'État',
            'Localisation',
            'Prix Achat',
            'Prix Vente',
            'Bénéfice Potentiel',
            'Fournisseur',
            'Condition',
            'Notes',
        ];
    }

    public function map($product): array
    {
        return [
            $product->created_at?->format('d/m/Y'),
            $product->productModel->brand ?? 'N/A',
            $product->productModel->name ?? 'N/A',
            $product->imei ?? '-',
            $product->serial_number ?? '-',
            $product->state->label(),
            $product->location->label(),
            $product->prix_achat,
            $product->prix_vente,
            $product->benefice_potentiel,
            $product->fournisseur ?? '-',
            $product->condition ?? '-',
            $product->notes ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }
}
