<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\ServiceArea;
use Illuminate\Database\Seeder;

class HenrikSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Location::query()->firstOrCreate(
            ['code' => 'MDO-CENTRAL'],
            [
                'name' => 'Municipalidad de Doñihue',
                'type' => 'site',
                'address' => 'Doñihue, Región de O’Higgins',
                'map_x' => 50,
                'map_y' => 50,
                'active' => true,
            ],
        );

        ServiceArea::query()->firstOrCreate(
            ['code' => 'INFORMATICA'],
            [
                'name' => 'Informática',
                'description' => 'Área inicial responsable del inventario tecnológico.',
                'active' => true,
            ],
        );
    }
}
