<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page()
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_users_are_redirected_to_the_products_page()
    {
        $response = $this->actingAs(User::factory()->create())->get('/');

        $response->assertRedirect(route('products.index', absolute: false));
    }
}
