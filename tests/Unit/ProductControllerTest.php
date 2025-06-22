<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Products;
use App\Models\Prices;
use App\Models\Currencies;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    private $token;

    protected function setUp(): void
    {
        parent::setUp();

        // crear un usuario para autenticación
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        // realizar login y obtener el token JWT
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $this->token = $response->json('token');
    }

    private function withAuthHeader()
    {
        return ['Authorization' => "Bearer {$this->token}"];
    }

    public function test_index_products()
    {
        Products::factory()->count(3)->create();

        $response = $this->getJson('/api/products', $this->withAuthHeader());

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    public function test_store_product()
    {
        $currency = Currencies::factory()->create();

        $response = $this->postJson('/api/products', [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 100.50,
            'currency_id' => $currency->id,
            'tax_cost' => 10.00,
            'manufacturing_cost' => 20.00,
        ], $this->withAuthHeader());

        $response->assertStatus(201);
        $response->assertJsonStructure(['id', 'name', 'description', 'price', 'currency_id']);
    }

    public function test_show_product()
    {
        $product = Products::factory()->create();

        $response = $this->getJson("/api/products/{$product->id}", $this->withAuthHeader());

        $response->assertStatus(200);
        $response->assertJson(['id' => $product->id]);
    }

    public function test_update_product()
    {
        $product = Products::factory()->create();
        $currency = Currencies::factory()->create();

        $response = $this->putJson("/api/products/{$product->id}/edit", [
            'name' => 'Updated Product',
            'description' => 'Updated Description',
            'price' => 150.00,
            'currency_id' => $currency->id,
            'tax_cost' => 15.00,
            'manufacturing_cost' => 25.00,
        ], $this->withAuthHeader());

        $response->assertStatus(200);
        $response->assertJson(['name' => 'Updated Product']);
    }

    public function test_destroy_product()
    {
        $product = Products::factory()->create();

        $response = $this->deleteJson("/api/products/{$product->id}", [], $this->withAuthHeader());

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Product deleted successfully']);
    }

    public function test_get_product_prices()
    {
        $product = Products::factory()->create();
        Prices::factory()->count(2)->create(['product_id' => $product->id]);

        $response = $this->getJson("/api/products/{$product->id}/prices", $this->withAuthHeader());

        $response->assertStatus(200);
        $response->assertJsonCount(2);
    }

    public function test_add_price()
    {
        $product = Products::factory()->create();
        $currency = Currencies::factory()->create();

        $response = $this->postJson("/api/products/{$product->id}/prices", [
            'currency_id' => $currency->id,
        ], $this->withAuthHeader());

        $response->assertStatus(201);
        $response->assertJsonStructure(['id', 'product_id', 'currency_id', 'price']);
    }
}