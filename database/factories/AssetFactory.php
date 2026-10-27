<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\Item;
use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Asset>
 */
class AssetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'item_id' => Item::factory()->state(['tracking_type' => 'serialized', 'type' => 'equipment']),
            'location_id' => Location::factory(),
            'asset_tag' => fake()->unique()->bothify('MDO-#####'),
            'serial_number' => fake()->unique()->bothify('SN-########'),
            'status' => 'in_stock',
        ];
    }
}
