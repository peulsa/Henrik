<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Location;
use App\Models\Stock;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Stock>
 */
class StockFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'item_id' => Item::factory(),
            'location_id' => Location::factory(),
            'quantity' => fake()->numberBetween(0, 30),
        ];
    }
}
