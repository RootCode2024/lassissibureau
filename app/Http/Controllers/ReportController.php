<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\StockMovement;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(
        private ReportService $reportService
    ) {}

    /**
     * Rapport par période.
     */
    public function daily(Request $request)
    {
        $startDate = $request->input('start_date', now()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $userId = $request->user()->isVendeur() ? $request->user()->id : null;

        $report = $this->reportService->getFullPeriodReport($startDate, $endDate);

        return view('reports.daily', compact('report', 'startDate', 'endDate'));
    }

    /**
     * Télécharger le rapport PDF.
     */
    public function downloadPdf(Request $request)
    {
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
     */
    public function weekly(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfWeek()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfWeek()->format('Y-m-d'));
        $userId = $request->user()->isVendeur() ? $request->user()->id : null;

        $report = $this->reportService->getWeeklyReport($startDate, $endDate, $userId);

        return view('reports.weekly', compact('report', 'startDate', 'endDate'));
    }

    /**
     * Rapport mensuel.
     */
    public function monthly(Request $request)
    {
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);
        $userId = $request->user()->isVendeur() ? $request->user()->id : null;

        $report = $this->reportService->getMonthlyReport($year, $month, $userId);

        return view('reports.monthly', compact('report', 'year', 'month'));
    }

    /**
     * Vue d'ensemble (Admin only).
     */
    public function overview()
    {
        $this->authorize('viewAny', \App\Models\Sale::class);

        $overview = $this->reportService->getOverview();

        return view('reports.overview', compact('overview'));
    }

    /**
     * Rapport produits (Admin only).
     */
    public function products()
    {
        $report = $this->reportService->getProductsReport();

        return view('reports.products', compact('report'));
    }

    /**
     * Rapport revendeurs (Admin only).
     */
    public function resellers(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $report = $this->reportService->getResellersReport($startDate, $endDate);

        return view('reports.resellers', compact('report', 'startDate', 'endDate'));
    }

    /**
     * Rapport inventaire (Admin only).
     */
    public function inventory()
    {
        $report = $this->reportService->getInventoryReport();

        return view('reports.inventory', compact('report'));
    }

    /**
     * Exporter les ventes (Admin only).
     */
    public function exportSales(Request $request)
    {
        $this->authorize('export', \App\Models\Sale::class);

        $validated = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        // TODO: Implémenter l'export Excel/CSV avec Laravel Excel
        // Pour l'instant, retour simple

        return back()->with('info', 'Export en cours de développement.');
    }

    /**
     * Exporter l'inventaire (Admin only).
     */
    public function exportInventory(Request $request)
    {
        $this->authorize('export', \App\Models\Sale::class);

        // TODO: Implémenter l'export Excel/CSV

        return back()->with('info', 'Export en cours de développement.');
    }
}