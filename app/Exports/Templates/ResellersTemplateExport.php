<?php

namespace App\Exports\Templates;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ResellersTemplateExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    public function array(): array
    {
        return [
            ['Boutique Alpha', '+2250707070707', '', 'Abidjan, Cocody'],
            ['Kouame Cellulaire', '0102030405', '0504030201', 'Bouaké, Marché'],
        ];
    }

    public function headings(): array
    {
        return [
            'nom',
            'telephone_1',
            'telephone_2',
            'adresse',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
