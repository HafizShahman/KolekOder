<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductReorderTest extends TestCase
{
    use RefreshDatabase;

    public function test_shop_can_reorder_products()
    {
        $user = User::create([
            'name' => 'Shop Owner',
            'email' => 'shop@example.com',
            'password' => bcrypt('password'),
            'role' => 'shop',
        ]);
        
        $shop = Shop::create([
            'user_id' => $user->id,
            'shop_name' => 'Test Shop',
        ]);
        
        // Ensure roles are set up if your app uses spatie/laravel-permission or similar
        // For now, I'll assume the middleware check is sufficient if we bypass it or handle it in the test.
        // If 'role:shop' is strict, we might need more setup.

        $p1 = Product::create(['shop_id' => $shop->id, 'name' => 'A', 'price' => 10, 'sort_order' => 1]);
        $p2 = Product::create(['shop_id' => $shop->id, 'name' => 'B', 'price' => 20, 'sort_order' => 2]);
        $p3 = Product::create(['shop_id' => $shop->id, 'name' => 'C', 'price' => 30, 'sort_order' => 3]);

        $this->actingAs($user, 'sanctum');

        $response = $this->putJson('/api/shop/settings/products/reorder', [
            'product_ids' => [$p3->id, $p1->id, $p2->id]
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Products order updated!']);

        $this->assertEquals(0, $p3->fresh()->sort_order);
        $this->assertEquals(1, $p1->fresh()->sort_order);
        $this->assertEquals(2, $p2->fresh()->sort_order);
    }
}
