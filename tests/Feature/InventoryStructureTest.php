<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\ItemType;
use App\Models\Location;
use Database\Seeders\HenrikSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class InventoryStructureTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_initial_data_can_be_seeded_more_than_once(): void
    {
        $this->seed(HenrikSeeder::class);
        ItemType::query()->where('slug', 'equipment')->firstOrFail()->update(['slug' => 'equipment-custom']);

        $this->seed(HenrikSeeder::class);

        $this->assertSame(1, ItemType::query()->where('name', 'Equipo')->count());
    }

    public function test_inventory_types_can_be_created_edited_and_deleted(): void
    {
        $this->post('/item-types', ['name' => 'Impresora', 'slug' => 'printer'])
            ->assertRedirect();

        $type = ItemType::query()->where('slug', 'printer')->firstOrFail();

        $this->put('/item-types/'.$type->id, [
            'name' => 'Impresora multifunción',
            'slug' => 'multifunction-printer',
            'active' => true,
        ])->assertRedirect('/item-types');

        $this->assertDatabaseHas('item_types', ['id' => $type->id, 'slug' => 'multifunction-printer']);

        $this->delete('/item-types/'.$type->id)->assertRedirect();
        $this->assertDatabaseMissing('item_types', ['id' => $type->id]);
    }

    public function test_type_with_associated_articles_cannot_be_deleted(): void
    {
        $type = ItemType::factory()->create(['slug' => 'peripheral']);
        Item::factory()->create(['type' => $type->slug]);

        $this->delete('/item-types/'.$type->id)->assertSessionHasErrors('type');

        $this->assertDatabaseHas('item_types', ['id' => $type->id]);
    }

    public function test_article_creation_does_not_require_unit_or_tracking_fields(): void
    {
        $type = ItemType::factory()->create(['slug' => 'device']);
        $location = Location::factory()->create();

        $this->post('/items', [
            'sku' => 'NOTE-001',
            'name' => 'Notebook municipal',
            'category' => 'Computadores',
            'type' => $type->slug,
            'location_id' => $location->id,
            'default_min_stock' => 1,
        ])->assertRedirect();

        $this->assertDatabaseHas('items', [
            'sku' => 'NOTE-001',
            'unit' => 'unidad',
            'tracking_type' => 'serialized',
        ]);
    }

    public function test_location_accepts_real_coordinates_and_has_inventory_view(): void
    {
        $this->post('/locations', [
            'name' => 'Alcaldía',
            'code' => 'MDO-ALC',
            'type' => 'office',
            'latitude' => -34.2264,
            'longitude' => -70.9596,
            'maps_url' => 'https://maps.google.com/?q=-34.2264,-70.9596',
        ])->assertRedirect();

        $location = Location::query()->where('code', 'MDO-ALC')->firstOrFail();
        $this->get('/locations/'.$location->id.'/inventory')
            ->assertOk()
            ->assertSee('Inventario de la ubicación')
            ->assertSee('Agregar dispositivo');
    }

    public function test_reception_screen_requires_an_existing_article(): void
    {
        Item::factory()->create(['name' => 'Monitor existente']);
        Location::factory()->create();

        $this->get('/stock/receive')
            ->assertOk()
            ->assertSee('Busca antes de crear')
            ->assertSee('Crear ficha rápida')
            ->assertDontSee('Documento de referencia')
            ->assertSee('Monitor existente');

        $this->get('/movements/create')->assertOk()->assertDontSee('Referencia');
    }
}
