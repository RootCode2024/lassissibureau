<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductModel;
use App\Models\Reseller;
use App\Models\Sale;
use App\Enums\ProductState;
use App\Enums\ProductLocation;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ResellerWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected ProductModel $productModel;
    protected Reseller $reseller;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        
        $this->productModel = ProductModel::factory()->create([
            'category' => 'telephone',
            'brand' => 'Apple',
            'name' => 'iPhone 13',
        ]);
        
        $this->reseller = Reseller::factory()->create([
            'name' => 'Revendeur Test',
            'phone' => '0700000001',
            'is_active' => true,
        ]);
    }

    /** @test */
    public function it_can_create_reseller_deposit_sale()
    {
        $this->actingAs($this->user);

        $product = Product::factory()->create([
            'product_model_id' => $this->productModel->id,
            'imei' => '444444444444444',
            'state' => ProductState::DISPONIBLE,
            'location' => ProductLocation::BOUTIQUE,
            'prix_achat' => 150000,
            'prix_vente' => 200000,
        ]);

        // Vente en dépôt (sans confirmation immédiate)
        $response = $this->post(route('sales.store'), [
            'product_id' => $product->id,
            'sale_type' => 'achat_direct',
            'buyer_type' => 'reseller',
            'reseller_id' => $this->reseller->id,
            'date_depot_revendeur' => now()->format('Y-m-d'),
            'prix_vente' => 200000,
            'prix_achat_produit' => 150000,
            'payment_status' => 'unpaid',
            'payment_due_date' => now()->addDays(30)->format('Y-m-d'),
            'date_vente_effective' => now()->format('Y-m-d'),
            'is_confirmed' => false, // DÉPÔT
        ]);

        $sale = Sale::first();
        
        // Assertions vente
        $this->assertFalse($sale->is_confirmed);
        $this->assertEquals($this->reseller->id, $sale->reseller_id);
        $this->assertEquals('unpaid', $sale->payment_status->value);
        $this->assertEquals(0, $sale->amount_paid);
        $this->assertEquals(200000, $sale->amount_remaining);
        
        // Le produit doit être CHEZ_REVENDEUR
        $product->refresh();
        $this->assertEquals(ProductState::DISPONIBLE, $product->state);
        $this->assertEquals(ProductLocation::CHEZ_REVENDEUR, $product->location);
    }

    /** @test */
    public function it_can_create_reseller_direct_sale()
    {
        $this->actingAs($this->user);

        $product = Product::factory()->create([
            'product_model_id' => $this->productModel->id,
            'imei' => '555555555555555',
            'state' => ProductState::DISPONIBLE,
            'location' => ProductLocation::BOUTIQUE,
            'prix_achat' => 100000,
            'prix_vente' => 150000,
        ]);

        // Vente directe au revendeur (confirmation immédiate)
        $response = $this->post(route('sales.store'), [
            'product_id' => $product->id,
            'sale_type' => 'achat_direct',
            'buyer_type' => 'reseller',
            'reseller_id' => $this->reseller->id,
            'date_depot_revendeur' => now()->format('Y-m-d'),
            'prix_vente' => 150000,
            'prix_achat_produit' => 100000,
            'payment_status' => 'paid',
            'amount_paid' => 150000,
            'date_vente_effective' => now()->format('Y-m-d'),
            'is_confirmed' => true, // VENTE DIRECTE
        ]);

        $sale = Sale::first();
        
        // Assertions
        $this->assertTrue($sale->is_confirmed);
        $this->assertEquals('paid', $sale->payment_status->value);
        $this->assertEquals(150000, $sale->amount_paid);
        
        // Le produit doit être VENDU (pas en dépôt)
        $product->refresh();
        $this->assertEquals(ProductState::VENDU, $product->state);
        $this->assertEquals(ProductLocation::CHEZ_CLIENT, $product->location);
    }

    /** @test */
    public function it_can_confirm_reseller_sale()
    {
        $this->actingAs($this->user);

        $product = Product::factory()->create([
            'product_model_id' => $this->productModel->id,
            'imei' => '666666666666666',
            'state' => ProductState::DISPONIBLE,
            'location' => ProductLocation::CHEZ_REVENDEUR,
        ]);

        // Créer une vente en dépôt
        $sale = Sale::factory()->create([
            'product_id' => $product->id,
            'reseller_id' => $this->reseller->id,
            'is_confirmed' => false,
            'prix_vente' => 180000,
            'prix_achat_produit' => 120000,
            'payment_status' => 'unpaid',
            'amount_paid' => 0,
            'amount_remaining' => 180000,
            'sold_by' => $this->user->id,
        ]);

        // Confirmer la vente
        $response = $this->post(route('sales.confirm', $sale), [
            'payment_amount' => 180000,
            'payment_method' => 'cash',
        ]);

        $sale->refresh();
        
        // La vente doit être confirmée
        $this->assertTrue($sale->is_confirmed);
        $this->assertNotNull($sale->date_confirmation_vente);
        
        // Le produit doit être VENDU
        $product->refresh();
        $this->assertEquals(ProductState::VENDU, $product->state);
        $this->assertEquals(ProductLocation::CHEZ_CLIENT, $product->location);
    }

    /** @test */
    public function it_can_return_product_from_reseller()
    {
        $this->actingAs($this->user);

        $product = Product::factory()->create([
            'product_model_id' => $this->productModel->id,
            'imei' => '777777777777777',
            'state' => ProductState::DISPONIBLE,
            'location' => ProductLocation::CHEZ_REVENDEUR,
        ]);

        // Créer une vente en dépôt
        $sale = Sale::factory()->create([
            'product_id' => $product->id,
            'reseller_id' => $this->reseller->id,
            'is_confirmed' => false,
            'prix_vente' => 200000,
            'prix_achat_produit' => 150000,
            'payment_status' => 'unpaid',
            'sold_by' => $this->user->id,
        ]);

        // Retourner le produit
        $response = $this->post(route('sales.return', $sale), [
            'notes' => 'Produit non vendu',
        ]);

        // La vente doit être soft-deleted
        $this->assertTrue($sale->fresh()->trashed());
        
        // Le produit doit être DISPONIBLE à la BOUTIQUE
        $product->refresh();
        $this->assertEquals(ProductState::DISPONIBLE, $product->state);
        $this->assertEquals(ProductLocation::BOUTIQUE, $product->location);
    }

    /** @test */
    public function it_uses_retroactive_sale_date()
    {
        $this->actingAs($this->user);

        $product = Product::factory()->create([
            'product_model_id' => $this->productModel->id,
            'imei' => '888888888888888',
            'state' => ProductState::DISPONIBLE,
            'location' => ProductLocation::BOUTIQUE,
        ]);

        $depositDate = now()->subDays(5)->format('Y-m-d');

        // Créer vente en dépôt il y a 5 jours
        $sale = Sale::factory()->create([
            'product_id' => $product->id,
            'reseller_id' => $this->reseller->id,
            'date_depot_revendeur' => $depositDate,
            'date_vente_effective' => $depositDate, // Date du dépôt
            'is_confirmed' => false,
            'prix_vente' => 150000,
            'prix_achat_produit' => 100000,
            'sold_by' => $this->user->id,
        ]);

        // Confirmer aujourd'hui
        $this->post(route('sales.confirm', $sale), [
            'payment_amount' => 150000,
            'payment_method' => 'mobile_money',
        ]);

        $sale->refresh();
        
        // La date_vente_effective doit rester celle du dépôt (il y a 5 jours)
        $this->assertEquals($depositDate, $sale->date_vente_effective->format('Y-m-d'));
        
        // La date de confirmation est aujourd'hui
        $this->assertEquals(now()->format('Y-m-d'), $sale->date_confirmation_vente->format('Y-m-d'));
    }
}
