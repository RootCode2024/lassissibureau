<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Models\ProductModel;
use App\Services\ProductService;
use App\Http\Requests\StoreProductRequest;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class CreateProduct extends Component
{
    // Informations communes
    public $product_model_id = null;
    public $state = 'disponible';
    public $location = 'boutique';
    public $prix_achat = null;
    public $prix_vente = null;
    public $condition = '';
    public $date_achat;
    public $fournisseur = '';
    public $defauts = '';
    public $notes = '';

    // Produits multiples
    public $products = [];

    // États et localisations disponibles
    public $states = [
        ['value' => 'disponible', 'label' => 'Disponible'],
        ['value' => 'vendu', 'label' => 'Vendu'],
        ['value' => 'en_reparation', 'label' => 'En réparation'],
        ['value' => 'reserve', 'label' => 'Réservé'],
        ['value' => 'retour', 'label' => 'Retour client'],
    ];

    public $locations = [
        ['value' => 'boutique', 'label' => 'Boutique'],
        ['value' => 'entrepot', 'label' => 'Entrepôt'],
        ['value' => 'atelier', 'label' => 'Atelier'],
        ['value' => 'transit', 'label' => 'En transit'],
    ];

    public $conditions = [
        'Neuf',
        'Comme neuf',
        'Très bon état',
        'Bon état',
        'État correct',
        'Pour pièces',
    ];

    public function mount()
    {
        $this->date_achat = now()->format('Y-m-d');
        $this->addProduct();
    }

    #[Computed]
    public function productModels()
    {
        return ProductModel::orderBy('brand')->orderBy('name')->get();
    }

    #[Computed]
    public function selectedModel()
    {
        if (!$this->product_model_id) {
            return null;
        }

        return ProductModel::find($this->product_model_id);
    }

    public function addProduct()
    {
        $this->products[] = [
            'id' => uniqid(),
            'imei' => '',
            'serial_number' => '',
        ];
    }

    public function removeProduct($index)
    {
        if (count($this->products) > 1) {
            unset($this->products[$index]);
            $this->products = array_values($this->products);
        }
    }

    public function rules()
    {
        // Récupérer les règles de base depuis la Request
        $request = new StoreProductRequest();
        $baseRules = $request->rules();
        
        // Adapter les règles pour le contexte Livewire (champs communs)
        $rules = [
            'product_model_id' => $baseRules['product_model_id'],
            'state' => $baseRules['state'],
            'location' => $baseRules['location'],
            'prix_achat' => $baseRules['prix_achat'],
            'prix_vente' => $baseRules['prix_vente'],
            'condition' => $baseRules['condition'],
            'date_achat' => $baseRules['date_achat'],
            'fournisseur' => $baseRules['fournisseur'],
            'defauts' => $baseRules['defauts'],
            'notes' => $baseRules['notes'],
        ];

        // Validation dynamique des produits (IMEI et Serial)
        foreach ($this->products as $index => $product) {
            // Règles pour l'IMEI adaptées de StoreProductRequest
            $imeiRules = $baseRules['imei'];
            
            // Ajuster la règle unique pour ignorer l'ID actuel lors de la validation (si nécessaire)
            // Ici c'est de la création, donc unique strict
            
            // Pour le callback requiredIf, on doit l'exécuter dans le contexte
            // Simplification : on laisse la règle Rule::requiredIf telle quelle,
            // elle évaluera $this->product_model_id qui est accessible dans le composant
            
            $rules["products.{$index}.imei"] = $imeiRules;
            $rules["products.{$index}.serial_number"] = $baseRules['serial_number'];
        }

        return $rules;
    }

    public function messages()
    {
        $request = new StoreProductRequest();
        $messages = $request->messages();
        
        // Ajouter des messages spécifiques pour les index de tableau
        foreach ($this->products as $index => $product) {
            $messages["products.{$index}.imei.size"] = "L'IMEI du produit " . ($index + 1) . " doit contenir exactement 15 chiffres.";
            $messages["products.{$index}.imei.unique"] = "L'IMEI du produit " . ($index + 1) . " existe déjà dans la base de données.";
            $messages["products.{$index}.imei.regex"] = "L'IMEI du produit " . ($index + 1) . " est invalide.";
            $messages["products.{$index}.serial_number.unique"] = "Le numéro de série du produit " . ($index + 1) . " existe déjà.";
        }

        return $messages;
    }

    public function save(ProductService $productService)
    {
        $this->validate();

        $userId = Auth::id();
        $createdProducts = [];

        DB::transaction(function () use ($productService, $userId, &$createdProducts) {
            foreach ($this->products as $productData) {
                // Ne créer que si au moins l'IMEI ou le numéro de série est renseigné, ou si ni l'un ni l'autre n'est requis par le modèle
                // (Note: la validation a déjà vérifié les champs requis)
                if (!empty($productData['imei']) || !empty($productData['serial_number'])) {
                    
                    // Préparation des données comme dans StoreProductRequest::prepareForValidation
                    // Nettoyage IMEI
                    $imei = isset($productData['imei']) ? preg_replace('/[^0-9]/', '', $productData['imei']) : null;
                    
                    $data = [
                        'product_model_id' => $this->product_model_id,
                        'imei' => $imei,
                        'serial_number' => $productData['serial_number'] ?? null,
                        'state' => $this->state,
                        'location' => $this->location,
                        'prix_achat' => $this->prix_achat,
                        'prix_vente' => $this->prix_vente,
                        'condition' => $this->condition ?: null,
                        'date_achat' => $this->date_achat ?: null,
                        'fournisseur' => $this->fournisseur ?: null,
                        'defauts' => $this->defauts ?: null,
                        'notes' => $this->notes ?: null,
                        'created_by' => $userId,
                    ];
                    
                    // Utilisation du Service pour garantir la création du mouvement de stock
                    $createdProducts[] = $productService->createProduct($data);
                }
            }
        });

        $count = count($createdProducts);

        session()->flash('success', $count > 1
            ? "{$count} produits ont été créés avec succès (historique de stock généré) !"
            : "Le produit a été créé avec succès (historique de stock généré) !");

        return redirect()->route('products.index');
    }

    public function render()
    {
        return view('livewire.products.create-product')->layout('layouts.app', ['title' => 'Créer un produit']);
    }
}
