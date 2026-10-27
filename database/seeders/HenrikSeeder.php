<?php

namespace Database\Seeders;

use App\Models\ItemType;
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
        collect([
            ['name' => 'Equipo', 'slug' => 'equipment'],
            ['name' => 'Dispositivo', 'slug' => 'device'],
            ['name' => 'Repuesto', 'slug' => 'spare_part'],
            ['name' => 'Periférico', 'slug' => 'peripheral'],
            ['name' => 'Insumo', 'slug' => 'supply'],
        ])->each(function (array $type): void {
            $exists = ItemType::query()
                ->where('slug', $type['slug'])
                ->orWhere('name', $type['name'])
                ->exists();

            if (! $exists) {
                ItemType::query()->create([...$type, 'active' => true]);
            }
        });

        Location::query()->firstOrCreate(
            ['code' => 'MDO-CENTRAL'],
            [
                'name' => 'Municipalidad de Doñihue',
                'type' => 'site',
                'address' => 'Doñihue, Región de O’Higgins',
                'latitude' => -34.2264000,
                'longitude' => -70.9596000,
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
