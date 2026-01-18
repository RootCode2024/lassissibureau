<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(
        private ReportService $reportService
    ) {}

    /**
     * Rapport par période.
     * ✅ Vendeurs: Toutes les ventes (pas de filtre), mais SANS bénéfices
     * ✅ Admin: Tout voir avec bénéfices
     */
    public function daily(Request $request)
    {
        $user = $request->user();
        $startDate = $request->input('start_date', now()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        // ✅ Pas de filtrage par userId - vendeurs voient TOUTES les ventes
        $report = $this->reportService->getFullPeriodReport($startDate, $endDate);

        // ✅ Masquer les bénéfices si vendeur
        $canViewProfits = $user->isAdmin();

        return view('reports.daily', compact('report', 'startDate', 'endDate', 'canViewProfits'));
    }

    /**
     * Télécharger le rapport PDF.
     * ✅ Admin uniquement
     */
    public function downloadPdf(Request $request)
    {
        abort_unless($request->user()->isAdmin(), 403);

        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $validated['start_date'];
        $endDate = $validated['end_date'];

        $report = $this->reportService->getFullPeriodReport($startDate, $endDate);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf.complete', compact('report', 'startDate', 'endDate'))
            ->setPaper('a4', 'portrait');

        $filename = "rapport_{$startDate}_{$endDate}.pdf";

        return $pdf->download($filename);
    }

    /**
     * Rapport hebdomadaire.
     * ✅ Restrictions identiques
     */
    public function weekly(Request $request)
    {
        $user = $request->user();
        $startDate = $request->input('start_date', now()->startOfWeek()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfWeek()->format('Y-m-d'));

        $report = $this->reportService->getWeeklyReport($startDate, $endDate);
        $canViewProfits = $user->isAdmin();

        return view('reports.weekly', compact('report', 'startDate', 'endDate', 'canViewProfits'));
    }

    /**
     * Rapport mensuel.
     * ✅ Restrictions identiques
     */
    public function monthly(Request $request)
    {
        $user = $request->user();
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        $report = $this->reportService->getMonthlyReport($year, $month);
        $canViewProfits = $user->isAdmin();

        return view('reports.monthly', compact('report', 'year', 'month', 'canViewProfits'));
    }

    /**
     * Vue d'ensemble (Admin only).
     */
    public function overview(Request $request)
    {
        abort_unless($request->user()->isAdmin(), 403);

        $overview = $this->reportService->getOverview();

        return view('reports.overview', compact('overview'));
    }

    /**
     * Rapport produits (Admin only).
     */
    public function products(Request $request)
    {
        abort_unless($request->user()->isAdmin(), 403);

        $report = $this->reportService->getProductsReport();

        return view('reports.products', compact('report'));
    }

    /**
     * Rapport revendeurs (Admin only).
     */
    public function resellers(Request $request)
    {
        abort_unless($request->user()->isAdmin(), 403);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $report = $this->reportService->getResellersReport($startDate, $endDate);

        return view('reports.resellers', compact('report', 'startDate', 'endDate'));
    }

    /**
     * Rapport inventaire (Admin only).
     */
    public function inventory(Request $request)
    {
        abort_unless($request->user()->isAdmin(), 403);

        $report = $this->reportService->getInventoryReport();

        return view('reports.inventory', compact('report'));
    }

    /**
     * Exporter les ventes (Admin only).
     */
    public function exportSales(Request $request)
    {
        abort_unless($request->user()->isAdmin(), 403);

        $validated = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\SalesExport($validated['start_date'], $validated['end_date']),
            "ventes_{$validated['start_date']}_{$validated['end_date']}.xlsx"
        );
    }

    /**
     * Exporter l'inventaire (Admin only).
     */
    public function exportInventory(Request $request)
    {
        abort_unless($request->user()->isAdmin(), 403);

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\InventoryExport,
            'inventaire_'.now()->format('Y-m-d').'.xlsx'
        );
    }
}