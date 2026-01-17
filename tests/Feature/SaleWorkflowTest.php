<?php

namespace Tests\Feature;

use App\Enums\ProductLocation;
use App\Enums\ProductState;
use App\Models\Product;
use App\Models\ProductModel;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Sales\CreateSale;
use Tests\TestCase;

class SaleWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected ProductModel $productModel;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);

        // Créer un utilisateur de test
        $this->user = User::factory()->create();
        $this->user->assignRole('admin');

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

        // Utiliser Livewire pour créer la vente
        Livewire::test(CreateSale::class)
            ->set('product_id', $product->id)
            ->set('sale_type', 'achat_direct')
            ->set('buyer_type', 'direct')
            ->set('prix_vente', 150000)
            ->set('prix_achat_produit', 100000)
            ->set('amount_paid', 150000)
            ->set('client_name', 'John Doe')
            ->set('client_phone', '0700000000')
            ->set('payment_option', 'paid')
            ->set('payment_method', 'cash')
            ->call('save')
            ->assertRedirect(route('sales.show', Sale::first()));

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

        Livewire::test(CreateSale::class)
            ->set('product_id', $product->id)
            ->set('sale_type', 'troc')
            ->set('buyer_type', 'direct')
            ->set('prix_vente', 300000)
            ->set('prix_achat_produit', 200000)
            ->set('amount_paid', 200000)
            ->set('client_name', 'Jane Doe')
            ->set('payment_option', 'paid')
            ->set('trade_in_modele_recu', 'iPhone 12')
            ->set('trade_in_imei_recu', '999999999999999')
            ->set('trade_in_valeur_reprise', 100000)
            ->set('trade_in_etat_recu', 'Bon état')
            ->call('save')
            ->assertHasNoErrors();

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

        Livewire::test(CreateSale::class)
            ->set('product_id', $product->id)
            ->set('sale_type', 'achat_direct')
            ->set('buyer_type', 'direct')
            ->set('prix_vente', 150000)
            ->set('prix_achat_produit', 100000)
            ->set('payment_option', 'paid')
            ->set('amount_paid', 150000)
            ->call('save')
            ->assertHasErrors(['product_id']);

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

        Livewire::test(CreateSale::class)
            ->set('product_id', $product->id)
            ->set('sale_type', 'achat_direct')
            ->set('buyer_type', 'direct')
            ->set('prix_vente', 120000)
            ->set('prix_achat_produit', 80000)
            ->set('amount_paid', 120000)
            ->set('payment_option', 'paid')
            ->call('save')
            ->assertHasNoErrors();

        $sale = Sale::first();

        // Bénéfice = 120k - 80k = 40k
        $this->assertEquals(40000, $sale->benefice);
    }
}
