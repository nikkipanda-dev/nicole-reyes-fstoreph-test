<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Adjectives used to build realistic-sounding product names.
     *
     * @var list<string>
     */
    protected array $adjectives = [
        'Premium', 'Deluxe', 'Compact', 'Portable', 'Wireless', 'Rechargeable',
        'Ergonomic', 'Eco-Friendly', 'Heavy-Duty', 'Lightweight', 'Adjustable',
        'Stainless Steel', 'Handcrafted', 'Waterproof', 'Foldable', 'Classic',
    ];

    /**
     * Product nouns used to build realistic-sounding product names.
     *
     * @var list<string>
     */
    protected array $nouns = [
        'Backpack', 'Headphones', 'Water Bottle', 'Desk Lamp', 'Office Chair',
        'Coffee Maker', 'Bluetooth Speaker', 'Running Shoes', 'Yoga Mat',
        'Sunglasses', 'Wristwatch', 'Laptop Stand', 'Kitchen Knife Set',
        'Winter Jacket', 'Wireless Mouse', 'Camping Tent', 'Blender',
        'Phone Case', 'Notebook', 'Desk Organizer',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_name' => fake()->randomElement($this->adjectives)
                .' '.fake()->randomElement($this->nouns),
            'product_description' => fake()->paragraph(),
            'quantity' => fake()->numberBetween(0, 500),
            'price' => fake()->randomFloat(2, 1, 1000),
            'status' => fake()->randomElement(['draft', 'published', 'archived']),
        ];
    }
}
