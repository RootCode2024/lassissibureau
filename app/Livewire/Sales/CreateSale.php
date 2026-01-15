<?php

namespace App\Livewire\Sales;

use App\Models\Product;
use App\Models\Reseller;
use App\Services\SaleService;
use App\Http\Requests\StoreSaleRequest;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class CreateSale extends Component
{
    // Produit
    public ?Product $preselectedProduct = null;
    public $product_id = null;
    public $prix_vente = null;
    public $prix_achat_produit = null;

    // Type de vente
    public $sale_type = 'achat_direct';

    // Type d'acheteur
    public $buyer_type = 'direct';

    // Client
    public $client_name = null;
    public $client_phone = null;

    // Revendeur
    public $reseller_id = null;
    public $date_depot_revendeur = null;
    public $reseller_confirm_immediate = false; // Nouvelle option

    // Paiement
    public $payment_status = 'unpaid';
    public $payment_option = 'unpaid';
    public $amount_paid = 0;
    public $payment_due_date = null;
    public $payment_method = 'cash';

    // Troc
    public $has_trade_in = false;
    public $trade_in_modele_recu = null;
    public $trade_in_imei_recu = null;
    public $trade_in_valeur_reprise = 0;
    public $trade_in_complement_especes = 0;
    public $trade_in_etat_recu = null;

    // Notes
    public $notes = null;

    // Collections
    public $availableProducts;
    public $resellers;

    public function mount(?int $productId = null)
    {
        // Initialiser les dates
        $this->date_depot_revendeur = now()->format('Y-m-d');
        $this->payment_due_date = now()->addDays(30)->format('Y-m-d');

        // Charger les produits disponibles
        $this->availableProducts = Product::availableForSale()
            ->with('productModel')
            ->get();

        // Charger les revendeurs actifs
        $this->resellers = Reseller::where('is_active', true)
            ->orderBy('name')
            ->get();

        // Si produit présélectionné
        if ($productId) {
            $this->preselectedProduct = Product::with('productModel')->find($productId);
            if ($this->preselectedProduct) {
                $this->product_id = $this->preselectedProduct->id;
                $this->prix_vente = $this->preselectedProduct->prix_vente;
                $this->prix_achat_produit = $this->preselectedProduct->prix_achat;
            }
        }
    }

    public function updatedProductId($value)
    {
        if ($value) {
            $product = Product::find($value);
            if ($product) {
                $this->prix_vente = $product->prix_vente;
                $this->prix_achat_produit = $product->prix_achat;
                $this->calculateComplement();
            }
        }
    }

    public function updatedSaleType($value)
    {
        $this->has_trade_in = ($value === 'troc');
        if ($this->has_trade_in) {
            $this->calculateComplement();
        }
    }

    public function updatedBuyerType($value)
    {
        if ($value === 'direct') {
            $this->payment_status = 'paid';
            $this->payment_option = 'paid';
            $this->amount_paid = $this->prix_vente;
        } else {
            $this->payment_status = 'unpaid';
            $this->payment_option = 'unpaid';
            $this->amount_paid = 0;
        }
    }

    public function updatedPaymentOption($value)
    {
        $this->payment_status = $value;
        if ($value === 'unpaid') {
            $this->amount_paid = 0;
        }
    }

    public function updatedTradeInValeurReprise($value)
    {
        $this->calculateComplement();
    }

    public function updatedPrixVente($value)
    {
        $this->calculateComplement();
    }

    private function calculateComplement()
    {
        if ($this->has_trade_in) {
            $prixVente = (float) ($this->prix_vente ?? 0);
            $valeurReprise = (float) ($this->trade_in_valeur_reprise ?? 0);
            $this->trade_in_complement_especes = $prixVente - $valeurReprise;
        }
    }

    public function rules()
    {
        $request = new StoreSaleRequest();
        $rules = $request->rules();

        // Adapter les clés nested (trade_in.xyz) vers les propriétés plates (trade_in_xyz)
        $mappedRules = [];
        foreach ($rules as $key => $rule) {
            $newKey = str_replace('trade_in.', 'trade_in_', $key);
            $mappedRules[$newKey] = $rule;
        }

        // Ajouter/Surcharger des règles spécifiques pour Livewire si nécessaire
        // Par exemple 'date_vente_effective' n'est pas un champ input dans le form Livewire (auto généré)
        // Mais gardons-le pour la structure, on le passera manuellement à la validation si nécessaire ou on l'exclut.
        // Ici le composant met la date auto dans 'save', donc on peut retirer 'date_vente_effective' de la validation INPUT
        unset($mappedRules['date_vente_effective']);
        unset($mappedRules['is_confirmed']); // Géré logiquement

        return $mappedRules;
    }

    public function messages()
    {
        $request = new StoreSaleRequest();
        $messages = $request->messages();
        
        // Adapter les clés des messages
        $mappedMessages = [];
        foreach ($messages as $key => $message) {
            $newKey = str_replace('trade_in.', 'trade_in_', $key);
            $mappedMessages[$newKey] = $message;
        }
        
        return $mappedMessages;
    }

    public function save(SaleService $saleService)
    {
        // Validation avec les règles adaptées
        $validated = $this->validate();

        try {
            // Préparer les données
            $data = [
                'product_id' => $this->product_id,
                'sale_type' => $this->sale_type,
                'prix_vente' => $this->prix_vente,
                'prix_achat_produit' => $this->prix_achat_produit,
                'client_name' => $this->client_name,
                'client_phone' => $this->client_phone,
                'reseller_id' => $this->buyer_type === 'reseller' ? $this->reseller_id : null,
                'date_depot_revendeur' => $this->buyer_type === 'reseller' ? $this->date_depot_revendeur : null,
                'date_vente_effective' => now()->format('Y-m-d'),
                // LOGIQUE MODIFIÉE : Confirmé si Direct OU (Revendeur ET Confirmation Immédiate demandée)
                'is_confirmed' => $this->buyer_type === 'direct' || ($this->buyer_type === 'reseller' && $this->reseller_confirm_immediate),
                'payment_status' => $this->buyer_type === 'direct' ? 'paid' : $this->payment_status,
                'amount_paid' => $this->buyer_type === 'direct' ? $this->prix_vente : ($this->amount_paid ?? 0),
                'payment_due_date' => $this->buyer_type === 'reseller' ? $this->payment_due_date : null,
                'payment_method' => $this->payment_method,
                'sold_by' => Auth::id(),
                'notes' => $this->notes,
            ];

            // Ajouter les données de troc si nécessaire
            if ($this->has_trade_in && $this->sale_type === 'troc') {
                $data['has_trade_in'] = true;
                $data['trade_in'] = [
                    'modele_recu' => $this->trade_in_modele_recu,
                    'imei_recu' => $this->trade_in_imei_recu,
                    'valeur_reprise' => $this->trade_in_valeur_reprise,
                    'complement_especes' => $this->trade_in_complement_especes,
                    'etat_recu' => $this->trade_in_etat_recu,
                ];
            }

            // Créer la vente
            $sale = $saleService->createSale($data);

            session()->flash('success', 'Vente enregistrée avec succès.');

            return redirect()->route('sales.show', $sale);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erreur CreateSale: ' . $e->getMessage());
            \Illuminate\Support\Facades\Log::error($e->getTraceAsString());
            session()->flash('error', 'Erreur : ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.sales.create-sale')
            ->title('Nouvelle vente')
            ->layout('layouts.app', [
                'header' => 'Nouvelle vente'
            ]);
    }
}
