<?php

namespace Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sku' => fake()->unique()->bothify('SKU-#####'),
            'name' => fake()->words(3, true),
            'category' => 'Informática',
            'type' => 'supply',
            'tracking_type' => 'bulk',
            'unit' => 'unidad',
            'default_min_stock' => 3,
            'active' => true,
        ];
    }
}
