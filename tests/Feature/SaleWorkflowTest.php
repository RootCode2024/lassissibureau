<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductModel;
use App\Models\Sale;
use App\Enums\ProductState;
use App\Enums\ProductLocation;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SaleWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected ProductModel $productModel;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Créer un utilisateur de test
        $this->user = User::factory()->create();
        
        // Créer un modèle de produit
        $this->productModel = ProductModel::factory()->create([
            'category' => 'telephone',
            'brand' => 'Samsung',
            'name' => 'Galaxy S21',
        ]);
    }

    /** @test */
    public function it_can_create_a_direct_sale()
    {
        $this->actingAs($this->user);

        // Créer un produit disponible
        $product = Product::factory()->create([
            'product_model_id' => $this->productModel->id,
            'imei' => '123456789012345',
            'state' => ProductState::DISPONIBLE,
            'location' => ProductLocation::BOUTIQUE,
            'prix_achat' => 100000,
            'prix_vente' => 150000,
        ]);

        // Simuler une vente directe
        $response = $this->post(route('sales.store'), [
            'product_id' => $product->id,
            'sale_type' => 'achat_direct',
            'buyer_type' => 'direct',
            'prix_vente' => 150000,
            'prix_achat_produit' => 100000,
            'client_name' => 'John Doe',
            'client_phone' => '0700000000',
            'payment_method' => 'cash',
            'date_vente_effective' => now()->format('Y-m-d'),
            'is_confirmed' => true,
        ]);

        // Vérifier la redirection
        $this->assertTrue(Sale::count() === 1);
        
        $sale = Sale::first();
        
        // Assertions
        $this->assertEquals($product->id, $sale->product_id);
        $this->assertEquals(150000, $sale->prix_vente);
        $this->assertEquals(50000, $sale->benefice); // 150k - 100k
        $this->assertTrue($sale->is_confirmed);
        $this->assertEquals('paid', $sale->payment_status->value);
        
        // Vérifier l'état du produit
        $product->refresh();
        $this->assertEquals(ProductState::VENDU, $product->state);
        $this->assertEquals(ProductLocation::CHEZ_CLIENT, $product->location);
    }

    /** @test */
    public function it_can_create_a_sale_with_trade_in()
    {
        $this->actingAs($this->user);

        $product = Product::factory()->create([
            'product_model_id' => $this->productModel->id,
            'imei' => '111111111111111',
            'state' => ProductState::DISPONIBLE,
            'location' => ProductLocation::BOUTIQUE,
            'prix_achat' => 200000,
            'prix_vente' => 300000,
        ]);

        $response = $this->post(route('sales.store'), [
            'product_id' => $product->id,
            'sale_type' => 'troc',
            'buyer_type' => 'direct',
            'prix_vente' => 300000,
            'prix_achat_produit' => 200000,
            'client_name' => 'Jane Doe',
            'has_trade_in' => true,
            'trade_in' => [
                'modele_recu' => 'iPhone 12',
                'imei_recu' => '999999999999999',
                'valeur_reprise' => 100000,
                'complement_especes' => 200000,
                'etat_recu' => 'Bon état',
            ],
            'payment_method' => 'cash',
            'date_vente_effective' => now()->format('Y-m-d'),
            'is_confirmed' => true,
        ]);

        $sale = Sale::first();
        
        // Vérifier le troc
        $this->assertNotNull($sale->tradeIn);
        $this->assertEquals('999999999999999', $sale->tradeIn->imei_recu);
        $this->assertEquals(100000, $sale->tradeIn->valeur_reprise);
        
        // Le produit vendu doit être VENDU
        $product->refresh();
        $this->assertEquals(ProductState::VENDU, $product->state);
    }

    /** @test */
    public function it_prevents_selling_unavailable_product()
    {
        $this->actingAs($this->user);

        // Produit déjà vendu
        $product = Product::factory()->create([
            'product_model_id' => $this->productModel->id,
            'imei' => '222222222222222',
            'state' => ProductState::VENDU,
            'location' => ProductLocation::CHEZ_CLIENT,
        ]);

        $response = $this->post(route('sales.store'), [
            'product_id' => $product->id,
            'sale_type' => 'achat_direct',
            'buyer_type' => 'direct',
            'prix_vente' => 150000,
            'prix_achat_produit' => 100000,
            'date_vente_effective' => now()->format('Y-m-d'),
            'is_confirmed' => true,
        ]);

        // Doit retourner une erreur
        $this->assertEquals(0, Sale::count());
    }

    /** @test */
    public function it_calculates_benefice_correctly()
    {
        $this->actingAs($this->user);

        $product = Product::factory()->create([
            'product_model_id' => $this->productModel->id,
            'imei' => '333333333333333',
            'state' => ProductState::DISPONIBLE,
            'location' => ProductLocation::BOUTIQUE,
            'prix_achat' => 80000,
            'prix_vente' => 120000,
        ]);

        $this->post(route('sales.store'), [
            'product_id' => $product->id,
            'sale_type' => 'achat_direct',
            'buyer_type' => 'direct',
            'prix_vente' => 120000,
            'prix_achat_produit' => 80000,
            'date_vente_effective' => now()->format('Y-m-d'),
            'is_confirmed' => true,
        ]);

        $sale = Sale::first();
        
        // Bénéfice = 120k - 80k = 40k
        $this->assertEquals(40000, $sale->benefice);
    }
}
