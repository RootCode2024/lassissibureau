<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport de Ventes - Du {{ $startDate }} au {{ $endDate }}</title>
    <style>
        @page {
            margin: 0;
            size: A4 portrait;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Inter', 'Segoe UI', sans-serif;
            font-size: 9px;
            color: #0a0a0a;
            background: #ffffff;
            padding: 25px;
        }
        
        /* MODERN HEADER */
        .hero {
            margin: -25px -25px 30px -25px;
            padding: 40px 25px;
            background: #000000;
            position: relative;
            overflow: hidden;
        }
        .hero::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #dc2626 0%, #00b341 100%);
        }
        .hero-title {
            font-size: 32px;
            font-weight: 900;
            color: #ffffff;
            letter-spacing: -1.5px;
            margin-bottom: 8px;
        }
        .hero-date {
            font-size: 11px;
            color: #999999;
            font-weight: 500;
        }
        
        /* MINIMAL STATS */
        .stats {
            display: table;
            width: 100%;
            margin: 30px 0;
            border-spacing: 15px 0;
        }
        .stat {
            display: table-cell;
            width: 33.33%;
            padding: 20px;
            background: #fafafa;
            border-radius: 0;
            position: relative;
        }
        .stat::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: #000000;
        }
        .stat.accent-green::before {
            background: #00b341;
        }
        .stat.accent-red::before {
            background: #dc2626;
        }
        .stat-label {
            font-size: 8px;
            font-weight: 600;
            color: #666666;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            margin-bottom: 10px;
        }
        .stat-value {
            font-size: 26px;
            font-weight: 900;
            color: #000000;
            letter-spacing: -1.5px;
            line-height: 1;
        }
        .stat-value.green {
            color: #00b341;
        }
        .stat-value.red {
            color: #dc2626;
        }
        .stat-suffix {
            font-size: 9px;
            font-weight: 600;
            color: #999999;
            margin-left: 4px;
        }
        
        /* SECTION */
        .section {
            margin: 40px 0 20px 0;
        }
        .section-title {
            font-size: 14px;
            font-weight: 800;
            color: #000000;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 1px solid #000000;
        }
        
        /* MODERN TABLE */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        thead {
            border-bottom: 2px solid #000000;
        }
        th {
            color: #000000;
            font-weight: 800;
            font-size: 7px;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 12px 8px;
            text-align: left;
        }
        tbody tr {
            border-bottom: 1px solid #f0f0f0;
        }
        tbody tr:hover {
            background: #fafafa;
        }
        td {
            padding: 14px 8px;
            font-size: 9px;
            color: #0a0a0a;
            vertical-align: middle;
        }
        td strong {
            font-weight: 700;
            color: #000000;
        }
        
        /* TROC ROW */
        .troc-row {
            background: #fafafa !important;
            border-left: 3px solid #000000 !important;
        }
        .troc-row td {
            padding: 12px 8px 12px 18px;
            font-size: 8px;
            color: #666666;
        }
        .troc-badge {
            display: inline-block;
            background: #000000;
            color: #ffffff;
            padding: 2px 6px;
            border-radius: 2px;
            font-weight: 700;
            font-size: 7px;
            margin-right: 8px;
        }
        
        /* BADGES */
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 2px;
            font-size: 7px;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .badge-success {
            background: #00b341;
            color: #ffffff;
        }
        .badge-danger {
            background: #dc2626;
            color: #ffffff;
        }
        
        /* SUMMARY */
        .summary {
            background: #fafafa;
            padding: 20px;
            margin: 20px 0;
            border-left: 3px solid #000000;
        }
        .summary-item {
            display: table;
            width: 100%;
            padding: 8px 0;
        }
        .summary-item:last-child {
            padding-top: 12px;
            margin-top: 8px;
            border-top: 1px solid #000000;
        }
        .summary-label {
            display: table-cell;
            font-size: 9px;
            font-weight: 600;
            color: #666666;
            width: 60%;
        }
        .summary-value {
            display: table-cell;
            font-size: 11px;
            font-weight: 800;
            color: #000000;
            text-align: right;
            width: 40%;
        }
        .summary-item:last-child .summary-value {
            font-size: 16px;
            color: #00b341;
        }
        
        /* UTILITIES */
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .page-break {
            page-break-after: always;
        }
        .empty {
            text-align: center;
            padding: 40px;
            color: #cccccc;
            font-size: 10px;
        }
        .green {
            color: #00b341;
        }
        .red {
            color: #dc2626;
        }
    </style>
</head>
<body>
    {{-- PAGE 1: VENTES --}}
    <div class="hero">
        <div class="hero-title">VENTES</div>
        <div class="hero-date">{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</div>
    </div>

    <div class="stats">
        <div class="stat">
            <div class="stat-label">Ventes</div>
            <div class="stat-value">{{ $report['stats']['total_sales'] }}</div>
        </div>
        <div class="stat">
            <div class="stat-label">Chiffre d'Affaires</div>
            <div class="stat-value">{{ number_format($report['stats']['total_revenue'], 0, ',', ' ') }}<span class="stat-suffix">FCFA</span></div>
        </div>
        <div class="stat accent-green">
            <div class="stat-label">Bénéfices</div>
            <div class="stat-value green">{{ number_format($report['stats']['total_profit'], 0, ',', ' ') }}<span class="stat-suffix">FCFA</span></div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Transactions</div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 9%">Date</th>
                <th style="width: 11%">Catégorie</th>
                <th style="width: 24%">Produit</th>
                <th style="width: 14%">IMEI/Série</th>
                <th style="width: 12%">Montant</th>
                <th style="width: 18%">Client</th>
                <th style="width: 7%">Heure</th>
            </tr>
        </thead>
        <tbody>
            @forelse($report['sales'] as $sale)
                <tr>
                    <td>{{ $sale->created_at->format('d/m') }}</td>
                    <td>{{ ucfirst($sale->product->productModel->category->value) }}</td>
                    <td><strong>{{ $sale->product->productModel->name }}</strong></td>
                    <td>{{ $sale->product->imei ?: $sale->product->serial_number ?: '—' }}</td>
                    <td class="text-right"><strong>{{ number_format($sale->prix_vente, 0, ',', ' ') }}</strong></td>
                    <td>{{ $sale->client_name ?: ($sale->reseller->name ?? '—') }}</td>
                    <td class="text-center">{{ $sale->created_at->format('H:i') }}</td>
                </tr>
                @if($sale->tradeIn)
                    <tr class="troc-row">
                        <td colspan="7">
                            <span class="troc-badge">TROC</span>
                            {{ $sale->tradeIn->modele_recu }} · 
                            {{ $sale->tradeIn->imei_recu }} · 
                            Reprise {{ number_format($sale->tradeIn->valeur_reprise, 0, ',', ' ') }} · 
                            Complément {{ number_format($sale->tradeIn->complement_especes, 0, ',', ' ') }}
                            @if($sale->tradeIn->etat_recu) · {{ $sale->tradeIn->etat_recu }}@endif
                        </td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="7" class="empty">Aucune vente</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="page-break"></div>

    {{-- PAGE 2: REVENDEURS --}}
    <div class="hero">
        <div class="hero-title">REVENDEURS</div>
        <div class="hero-date">{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</div>
    </div>

    <div class="stats">
        <div class="stat">
            <div class="stat-label">Sorties</div>
            <div class="stat-value">{{ $report['stats']['total_reseller_sales'] }}</div>
        </div>
        <div class="stat">
            <div class="stat-label">Total</div>
            <div class="stat-value">{{ number_format($report['reseller_sales']->sum('prix_vente'), 0, ',', ' ') }}<span class="stat-suffix">FCFA</span></div>
        </div>
        <div class="stat accent-red">
            <div class="stat-label">Impayés</div>
            <div class="stat-value red">{{ number_format($report['reseller_sales']->where('payment_status.value', '!=', 'paid')->sum('amount_remaining'), 0, ',', ' ') }}<span class="stat-suffix">FCFA</span></div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Dépôts</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 12%">Date</th>
                <th style="width: 22%">Revendeur</th>
                <th style="width: 30%">Produit</th>
                <th style="width: 14%">IMEI</th>
                <th style="width: 12%">Montant</th>
                <th style="width: 10%">Statut</th>
            </tr>
        </thead>
        <tbody>
            @forelse($report['reseller_sales'] as $sale)
                <tr>
                    <td>{{ $sale->date_depot_revendeur ? $sale->date_depot_revendeur->format('d/m/Y') : '—' }}</td>
                    <td><strong>{{ $sale->reseller->name ?? '—' }}</strong></td>
                    <td>{{ $sale->product->productModel->name }}</td>
                    <td>{{ $sale->product->imei ?: $sale->product->serial_number }}</td>
                    <td class="text-right"><strong>{{ number_format($sale->prix_vente, 0, ',', ' ') }}</strong></td>
                    <td class="text-center">
                        @if($sale->payment_status->value === 'paid')
                            <span class="badge badge-success">Payé</span>
                        @else
                            <span class="badge badge-danger">{{ number_format($sale->amount_remaining, 0, ',', ' ') }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="empty">Aucune sortie</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="page-break"></div>

    {{-- PAGE 3: STOCKS --}}
    <div class="hero">
        <div class="hero-title">STOCK</div>
        <div class="hero-date">{{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <div class="stats">
        <div class="stat">
            <div class="stat-label">Produits</div>
            <div class="stat-value">{{ $report['stats']['total_stock_available'] }}</div>
        </div>
        <div class="stat">
            <div class="stat-label">Valeur</div>
            <div class="stat-value">{{ number_format($report['stats']['total_stock_value'], 0, ',', ' ') }}<span class="stat-suffix">FCFA</span></div>
        </div>
        <div class="stat accent-green">
            <div class="stat-label">Bénéfice</div>
            <div class="stat-value green">{{ number_format($report['stocks']->sum(fn($p) => $p->prix_vente - $p->prix_achat), 0, ',', ' ') }}<span class="stat-suffix">FCFA</span></div>
        </div>
    </div>

    <div class="summary">
        <div class="summary-item">
            <span class="summary-label">Investissement total</span>
            <span class="summary-value">{{ number_format($report['stocks']->sum('prix_achat'), 0, ',', ' ') }} FCFA</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Valeur de vente</span>
            <span class="summary-value">{{ number_format($report['stats']['total_stock_value'], 0, ',', ' ') }} FCFA</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Bénéfice potentiel</span>
            <span class="summary-value">{{ number_format($report['stocks']->sum(fn($p) => $p->prix_vente - $p->prix_achat), 0, ',', ' ') }} FCFA</span>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Inventaire</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 46%">Modèle</th>
                <th style="width: 13%">Qté</th>
                <th style="width: 20%">Valeur</th>
                <th style="width: 21%">Bénéfice</th>
            </tr>
        </thead>
        <tbody>
            @php
                $groupedByModel = $report['stocks']->groupBy(fn($p) => $p->productModel->name)->map(function($products) {
                    return [
                        'name' => $products->first()->productModel->name,
                        'quantity' => $products->count(),
                        'total_value' => $products->sum('prix_vente'),
                        'total_profit' => $products->sum(fn($p) => $p->prix_vente - $p->prix_achat),
                    ];
                });
            @endphp
            @forelse($groupedByModel as $model)
                <tr>
                    <td><strong>{{ $model['name'] }}</strong></td>
                    <td class="text-center"><strong>{{ $model['quantity'] }}</strong></td>
                    <td class="text-right"><strong>{{ number_format($model['total_value'], 0, ',', ' ') }}</strong></td>
                    <td class="text-right green"><strong>{{ number_format($model['total_profit'], 0, ',', ' ') }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="empty">Aucun stock</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>