<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Location;
use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StockMovement>
 */
class StockMovementFactory extends Factory
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
            'to_location_id' => Location::factory(),
            'type' => 'receive',
            'quantity' => 1,
            'performed_by_email' => 'informatica@donihue.cl',
            'performed_by_name' => 'Equipo de Informática',
        ];
    }
}
