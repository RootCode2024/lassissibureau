<?php

namespace App\Http\Controllers;

use App\Imports\ProductModelsImport;
use App\Imports\ProductsImport;
use App\Imports\ResellersImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    /**
     * Affiche le formulaire d'import global.
     */
    public function index()
    {
        return view('imports.index');
    }

    /**
     * Traite l'import des Modèles de Produits.
     */
    public function storeModels(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        try {
            Excel::import(new ProductModelsImport, $request->file('file'));
            return back()->with('success', 'Modèles importés avec succès !');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            return back()->with('error', 'Erreur lors de l\'import. Vérifiez votre fichier.')
                         ->with('import_errors', $failures);
        } catch (\Exception $e) {
            return back()->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    /**
     * Traite l'import des Revendeurs.
     */
    public function storeResellers(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        try {
            Excel::import(new ResellersImport, $request->file('file'));
            return back()->with('success', 'Revendeurs importés avec succès !');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            return back()->with('error', 'Erreur lors de l\'import. Vérifiez votre fichier.')
                         ->with('import_errors', $failures);
        } catch (\Exception $e) {
            return back()->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    /**
     * Traite l'import des Produits (Stock).
     */
    public function storeProducts(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        try {
            Excel::import(new ProductsImport, $request->file('file'));
            return back()->with('success', 'Produits importés avec succès !');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            return back()->with('error', 'Erreur lors de l\'import. Vérifiez votre fichier.')
                         ->with('import_errors', $failures);
        } catch (\Exception $e) {
            return back()->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }
    
    /**
     * Télécharge un modèle de fichier Excel vierge (avec entêtes).
     */
    public function downloadTemplate(string $type) 
    {
        return match($type) {
            'models' => Excel::download(new \App\Exports\Templates\ProductModelsTemplateExport, 'modele_produits_template.xlsx'),
            'resellers' => Excel::download(new \App\Exports\Templates\ResellersTemplateExport, 'modele_revendeurs_template.xlsx'),
            'products' => Excel::download(new \App\Exports\Templates\ProductsTemplateExport, 'modele_stock_template.xlsx'),
            default => back()->with('error', 'Type de template inconnu.'),
        };
    }
}
