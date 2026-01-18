<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function __construct(
        private string $startDate,
        private string $endDate
    ) {}

    public function query()
    {
        // Utilisation de la même logique que ReportService pour la cohérence
        return Sale::query()
            ->with(['product.productModel', 'seller', 'reseller'])
            ->confirmed()
            ->whereBetween('date_vente_effective', [$this->startDate, $this->endDate])
            ->orderBy('date_vente_effective');
    }

    public function headings(): array
    {
        return [
            'Date Vente',
            'Produit',
            'Modèle',
            'IMEI',
            'Type Vente',
            'Client',
            'Téléphone Client',
            'Prix Vente',
            'Bénéfice',
            'Vendu Par',
            'Revendeur',
            'Date Paiement',
            'Statut Paiement',
        ];
    }

    public function map($sale): array
    {
        return [
            $sale->date_vente_effective?->format('d/m/Y'),
            $sale->product->productModel->name ?? 'N/A',
            $sale->product->productModel->brand ?? 'N/A',
            $sale->product->imei ?? 'N/A',
            $sale->sale_type->label(),
            $sale->client_name,
            $sale->client_phone,
            $sale->prix_vente,
            $sale->benefice,
            $sale->seller->name ?? 'N/A',
            $sale->reseller->name ?? '-',
            $sale->final_payment_date?->format('d/m/Y') ?? '-',
            $sale->payment_status->label(),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }
}
