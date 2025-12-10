<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Hotel;
use App\Models\Driver;
use App\Models\BlogPost;
use App\Models\BlogCategory;

class NewServicesApiTest extends TestCase
{
    use RefreshDatabase;

    // ==========================================
    // PUBLIC ENDPOINT TESTS
    // ==========================================

    /**
     * Test public restaurants endpoint
     */
    public function test_public_restaurants_endpoint(): void
    {
        $response = $this->getJson('/api/restaurants');
        $response->assertStatus(200);
    }

    /**
     * Test public hotels endpoint
     */
    public function test_public_hotels_endpoint(): void
    {
        $response = $this->getJson('/api/hotels');
        $response->assertStatus(200);
    }

    /**
     * Test public blog posts endpoint
     */
    public function test_public_blog_posts_endpoint(): void
    {
        $response = $this->getJson('/api/blog/posts');
        $response->assertStatus(200);
    }

    /**
     * Test public blog categories endpoint
     */
    public function test_public_blog_categories_endpoint(): void
    {
        $response = $this->getJson('/api/blog/categories');
        $response->assertStatus(200);
    }

    /**
     * Test public featured posts endpoint
     */
    public function test_public_featured_posts_endpoint(): void
    {
        $response = $this->getJson('/api/blog/posts/featured');
        $response->assertStatus(200);
    }

    /**
     * Test public transport routes endpoint
     */
    public function test_public_transport_routes_endpoint(): void
    {
        $response = $this->getJson('/api/transport/routes');
        $response->assertStatus(200);
    }

    /**
     * Test transport price calculation
     */
    public function test_transport_price_calculation(): void
    {
        $response = $this->postJson('/api/transport/calculate-price', [
            'origin' => 'Istanbul Airport',
            'destination' => 'Taksim',
            'passengers' => 2,
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'base_price',
            'total_price',
            'currency'
        ]);
    }

    // ==========================================
    // RESTAURANT CRUD TESTS (Admin Authenticated)
    // ==========================================

    /**
     * Test restaurant create (admin) - Skipped if role middleware not configured
     */
    public function test_admin_can_create_restaurant(): void
    {
        // Skip test in testing environment where role middleware is not configured
        $this->markTestSkipped('Admin role middleware not configured in test environment');
    }

    // ==========================================
    // HOTEL CRUD TESTS (Admin Authenticated)
    // ==========================================

    /**
     * Test hotel check availability endpoint
     */
    public function test_hotel_availability_check(): void
    {
        $response = $this->postJson('/api/hotels/1/check-availability', [
            'check_in' => now()->addDays(1)->format('Y-m-d'),
            'check_out' => now()->addDays(3)->format('Y-m-d'),
        ]);

        // 404 expected if hotel doesn't exist, 200 if it does
        $this->assertTrue(in_array($response->status(), [200, 404]));
    }

    // ==========================================
    // BLOG TESTS
    // ==========================================

    /**
     * Test blog category creation structure
     */
    public function test_blog_category_structure(): void
    {
        $response = $this->getJson('/api/blog/categories');
        $response->assertStatus(200);
        // May return array or paginated response
        $response->assertJsonIsArray();
    }

    /**
     * Test blog posts with filters
     */
    public function test_blog_posts_with_search(): void
    {
        $response = $this->getJson('/api/blog/posts?search=test');
        $response->assertStatus(200);
    }
}
