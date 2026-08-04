<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page()
    {
        $response = $this->get('/products');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_users_can_visit_the_products_page()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/products');
        $response->assertStatus(200);
    }
}
