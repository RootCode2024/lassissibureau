<?php

namespace Tests\Unit;

use App\Enums\ProductLocation;
use App\Enums\ProductState;
use App\Enums\StockMovementType;
use App\Models\Product;
use App\Models\ProductModel;
use App\Models\User;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    private ProductService $productService;
    private User $admin;
    private ProductModel $productModel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productService = app(ProductService::class);

        $this->admin = User::factory()->create();

        $this->productModel = ProductModel::factory()->create([
            'name' => 'Samsung Galaxy S23',
            'brand' => 'Samsung',
            'category' => 'telephone',
            'prix_revient_default' => 400000,
            'prix_vente_default' => 550000,
            'stock_minimum' => 2,
        ]);

        $this->actingAs($this->admin);
    }

    /** @test */
    public function it_can_create_a_product_with_stock_movement(): void
    {
        $productData = [
            'product_model_id' => $this->productModel->id,
            'imei' => '123456789012345',
            'state' => ProductState::DISPONIBLE->value,
            'location' => ProductLocation::BOUTIQUE->value,
            'prix_achat' => 400000,
            'prix_vente' => 550000,
            'date_achat' => now(),
            'fournisseur' => 'Fournisseur Test',
            'condition' => 'neuf',
            'created_by' => $this->admin->id,
        ];

        $product = $this->productService->createProduct($productData);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals('123456789012345', $product->imei);
        $this->assertEquals(ProductState::DISPONIBLE, $product->state);
        $this->assertEquals(ProductLocation::BOUTIQUE, $product->location);

        // Vérifier qu'un mouvement de stock a été créé
        $this->assertCount(1, $product->stockMovements);
        $this->assertEquals(
            StockMovementType::RECEPTION_FOURNISSEUR->value,
            $product->stockMovements->first()->type->value
        );
    }

    /** @test */
    public function it_can_update_product_prices(): void
    {
        $product = Product::factory()->create([
            'product_model_id' => $this->productModel->id,
            'prix_achat' => 400000,
            'prix_vente' => 550000,
            'created_by' => $this->admin->id,
        ]);

        $updatedProduct = $this->productService->updatePrices(
            $product,
            450000, // nouveau prix d'achat
            600000, // nouveau prix de vente
            $this->admin->id
        );

        $this->assertEquals(450000, $updatedProduct->prix_achat);
        $this->assertEquals(600000, $updatedProduct->prix_vente);
    }

    /** @test */
    public function it_can_send_product_to_repair(): void
    {
        $product = Product::factory()->create([
            'product_model_id' => $this->productModel->id,
            'state' => ProductState::A_REPARER->value,
            'location' => ProductLocation::BOUTIQUE->value,
            'created_by' => $this->admin->id,
        ]);

        $updatedProduct = $this->productService->sendToRepair(
            $product,
            $this->admin->id,
            'Écran cassé, envoi réparation'
        );

        $this->assertEquals(ProductState::A_REPARER, $updatedProduct->state);
        $this->assertEquals(ProductLocation::EN_REPARATION, $updatedProduct->location);

        // Vérifier le mouvement de stock
        $lastMovement = $updatedProduct->stockMovements->first();
        $this->assertEquals(StockMovementType::ENVOI_REPARATION->value, $lastMovement->type->value);
    }

    /** @test */
    public function it_can_mark_product_as_repaired(): void
    {
        $product = Product::factory()->create([
            'product_model_id' => $this->productModel->id,
            'state' => ProductState::A_REPARER->value,
            'location' => ProductLocation::EN_REPARATION->value,
            'created_by' => $this->admin->id,
        ]);

        $updatedProduct = $this->productService->markAsRepaired(
            $product,
            $this->admin->id,
            'Réparation terminée, écran remplacé'
        );

        $this->assertEquals(ProductState::REPARE, $updatedProduct->state);
        $this->assertEquals(ProductLocation::BOUTIQUE, $updatedProduct->location);
    }

    /** @test */
    public function it_can_find_product_by_imei(): void
    {
        Product::factory()->create([
            'product_model_id' => $this->productModel->id,
            'imei' => '987654321098765',
            'created_by' => $this->admin->id,
        ]);

        $foundProduct = $this->productService->findByImei('987654321098765');

        $this->assertNotNull($foundProduct);
        $this->assertEquals('987654321098765', $foundProduct->imei);
    }

    /** @test */
    public function it_returns_null_for_non_existing_imei(): void
    {
        $foundProduct = $this->productService->findByImei('000000000000000');

        $this->assertNull($foundProduct);
    }

    /** @test */
    public function it_can_get_available_products(): void
    {
        // Produit disponible
        Product::factory()->create([
            'product_model_id' => $this->productModel->id,
            'state' => ProductState::DISPONIBLE->value,
            'location' => ProductLocation::BOUTIQUE->value,
            'created_by' => $this->admin->id,
        ]);

        // Produit réparé (aussi disponible à la vente)
        Product::factory()->create([
            'product_model_id' => $this->productModel->id,
            'state' => ProductState::REPARE->value,
            'location' => ProductLocation::BOUTIQUE->value,
            'created_by' => $this->admin->id,
        ]);

        // Produit vendu (non disponible)
        Product::factory()->create([
            'product_model_id' => $this->productModel->id,
            'state' => ProductState::VENDU->value,
            'location' => ProductLocation::CHEZ_CLIENT->value,
            'created_by' => $this->admin->id,
        ]);

        $availableProducts = $this->productService->getAvailableProducts();

        $this->assertCount(2, $availableProducts);
    }

    /** @test */
    public function it_cannot_delete_sold_product(): void
    {
        $product = Product::factory()->create([
            'product_model_id' => $this->productModel->id,
            'state' => ProductState::VENDU->value,
            'location' => ProductLocation::CHEZ_CLIENT->value,
            'created_by' => $this->admin->id,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Impossible de supprimer un produit vendu ou chez un revendeur.');

        $this->productService->deleteProduct($product);
    }

    /** @test */
    public function it_can_delete_available_product(): void
    {
        $product = Product::factory()->create([
            'product_model_id' => $this->productModel->id,
            'state' => ProductState::DISPONIBLE->value,
            'location' => ProductLocation::BOUTIQUE->value,
            'created_by' => $this->admin->id,
        ]);

        $result = $this->productService->deleteProduct($product);

        $this->assertTrue($result);
        $this->assertSoftDeleted($product);
    }

    /** @test */
    public function it_calculates_product_stats_correctly(): void
    {
        $product = Product::factory()->create([
            'product_model_id' => $this->productModel->id,
            'state' => ProductState::DISPONIBLE->value,
            'location' => ProductLocation::BOUTIQUE->value,
            'prix_achat' => 400000,
            'prix_vente' => 550000,
            'created_by' => $this->admin->id,
        ]);

        $stats = $this->productService->getProductStats($product);

        $this->assertEquals(150000, $stats['benefice_potentiel']);
        $this->assertEquals(37.5, $stats['marge_percentage']);
        $this->assertTrue($stats['is_available']);
        $this->assertTrue($stats['is_in_store']);
    }

    /** @test */
    public function it_can_get_products_needing_attention(): void
    {
        // Produit à réparer
        Product::factory()->create([
            'product_model_id' => $this->productModel->id,
            'state' => ProductState::A_REPARER->value,
            'location' => ProductLocation::BOUTIQUE->value,
            'created_by' => $this->admin->id,
        ]);

        // Produit retour
        Product::factory()->create([
            'product_model_id' => $this->productModel->id,
            'state' => ProductState::RETOUR->value,
            'location' => ProductLocation::BOUTIQUE->value,
            'created_by' => $this->admin->id,
        ]);

        // Produit normal (ne nécessite pas d'attention)
        Product::factory()->create([
            'product_model_id' => $this->productModel->id,
            'state' => ProductState::DISPONIBLE->value,
            'location' => ProductLocation::BOUTIQUE->value,
            'created_by' => $this->admin->id,
        ]);

        $needingAttention = $this->productService->getProductsNeedingAttention();

        $this->assertCount(2, $needingAttention);
    }
}
