<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\AuditLog;
use App\Models\Item;
use App\Models\ItemType;
use App\Models\Location;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class InventoryWorkflowTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_item_creation_creates_zero_stock_and_actor_audit(): void
    {
        $mainLocation = $this->mainLocation();
        ItemType::factory()->create(['name' => 'Insumo', 'slug' => 'supply']);

        $this->post('/items', [...$this->itemPayload(), 'location_id' => $mainLocation->id])
            ->assertRedirect();

        $item = Item::query()->where('sku', 'TON-001')->firstOrFail();
        $this->assertDatabaseHas('stocks', [
            'item_id' => $item->id,
            'location_id' => $mainLocation->id,
            'quantity' => 0,
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'auditable_type' => Item::class,
            'auditable_id' => $item->id,
            'action' => 'created',
            'actor_email' => 'informatica@donihue.cl',
        ]);
    }

    public function test_receipt_increases_stock_and_records_actor(): void
    {
        $mainLocation = $this->mainLocation();
        $item = $this->createItem();

        $this->post('/movements', [
            'item_id' => $item->id,
            'type' => 'receive',
            'quantity' => 12,
            'to_location_id' => $mainLocation->id,
            'reference' => 'OC-100',
        ])->assertRedirect();

        $this->assertSame(12, Stock::query()->whereBelongsTo($item)->firstOrFail()->quantity);
        $this->assertDatabaseHas('stock_movements', [
            'item_id' => $item->id,
            'quantity' => 12,
            'performed_by_email' => 'informatica@donihue.cl',
        ]);
    }

    public function test_receipt_can_be_edited_and_deleted_with_stock_correction(): void
    {
        $location = $this->mainLocation();
        $item = $this->createItem(['location_id' => $location->id]);

        $this->post('/movements', [
            'item_id' => $item->id,
            'type' => 'receive',
            'quantity' => 8,
            'to_location_id' => $location->id,
            'notes' => 'Proveedor inicial',
        ])->assertRedirect();

        $movement = StockMovement::query()->firstOrFail();
        $this->put('/movements/'.$movement->id, [
            'item_id' => $item->id,
            'quantity' => 5,
            'to_location_id' => $location->id,
            'notes' => 'Proveedor corregido',
        ])->assertRedirect();
        $this->assertSame(5, Stock::query()->whereBelongsTo($item)->firstOrFail()->quantity);

        $this->delete('/movements/'.$movement->id)->assertRedirect('/movements');
        $this->assertSame(0, Stock::query()->whereBelongsTo($item)->firstOrFail()->quantity);
        $this->assertDatabaseMissing('stock_movements', ['id' => $movement->id]);
    }

    public function test_quick_item_creation_from_reception_assigns_location(): void
    {
        $location = $this->mainLocation();
        $type = ItemType::factory()->create(['slug' => 'peripheral']);

        $this->post('/stock/quick-item', [
            'sku' => 'MOU-001',
            'name' => 'Mouse USB',
            'category' => 'Periféricos',
            'type' => $type->slug,
            'location_id' => $location->id,
        ])->assertRedirect();

        $item = Item::query()->where('sku', 'MOU-001')->firstOrFail();
        $this->assertSame($location->id, $item->location_id);
        $this->get('/locations/'.$location->id.'/inventory')->assertSee('Mouse USB')->assertSee('Editar')->assertSee('Eliminar');
    }

    public function test_audit_explains_changes_in_plain_language(): void
    {
        $location = $this->mainLocation();
        $item = $this->createItem(['location_id' => $location->id]);
        $item->update(['name' => 'Tóner actualizado']);
        $stock = Stock::factory()->create(['item_id' => $item->id, 'location_id' => $location->id, 'quantity' => 0]);
        $stock->update(['quantity' => 10]);

        $this->get('/audit')
            ->assertOk()
            ->assertSee('Detalle comprensible')
            ->assertSee('Cambió nombre')
            ->assertSee('stock de')
            ->assertSee('Cambió cantidad de “0” a “10”')
            ->assertDontSee('auditable_type');
    }

    public function test_delivery_without_enough_stock_is_rejected_without_partial_changes(): void
    {
        $mainLocation = $this->mainLocation();
        $item = $this->createItem();
        Stock::factory()->create(['item_id' => $item->id, 'location_id' => $mainLocation->id, 'quantity' => 2]);

        $this->from('/movements/create')->post('/movements', [
            'item_id' => $item->id,
            'type' => 'deliver',
            'quantity' => 3,
            'from_location_id' => $mainLocation->id,
        ])->assertRedirect('/movements/create')->assertSessionHasErrors('quantity');

        $this->assertSame(2, Stock::query()->firstOrFail()->quantity);
        $this->assertSame(0, StockMovement::query()->count());
    }

    public function test_transfer_updates_both_locations_atomically(): void
    {
        $mainLocation = $this->mainLocation();
        $item = $this->createItem();
        $office = Location::factory()->create(['name' => 'Oficina TI', 'code' => 'MDO-TI']);
        Stock::factory()->create(['item_id' => $item->id, 'location_id' => $mainLocation->id, 'quantity' => 10]);
        Stock::factory()->create(['item_id' => $item->id, 'location_id' => $office->id, 'quantity' => 1]);

        $this->post('/movements', [
            'item_id' => $item->id,
            'type' => 'transfer',
            'quantity' => 4,
            'from_location_id' => $mainLocation->id,
            'to_location_id' => $office->id,
        ])->assertRedirect();

        $this->assertSame(6, Stock::query()->where('location_id', $mainLocation->id)->firstOrFail()->quantity);
        $this->assertSame(5, Stock::query()->where('location_id', $office->id)->firstOrFail()->quantity);
    }

    public function test_dashboard_displays_low_stock_alert(): void
    {
        $mainLocation = $this->mainLocation();
        $item = $this->createItem(['default_min_stock' => 5]);
        Stock::factory()->create(['item_id' => $item->id, 'location_id' => $mainLocation->id, 'quantity' => 2]);

        $this->get('/')->assertOk()->assertSee('Stock que requiere atención')->assertSee($item->name);
    }

    public function test_item_is_soft_deleted_and_history_is_retained(): void
    {
        $item = $this->createItem();

        $this->delete('/items/'.$item->id)->assertRedirect('/items');

        $this->assertSoftDeleted('items', ['id' => $item->id]);
        $this->assertTrue(AuditLog::query()->where('auditable_id', $item->id)->where('action', 'deleted')->exists());
    }

    public function test_device_can_be_identified_from_any_catalog_item(): void
    {
        $item = $this->createItem(['tracking_type' => 'bulk']);

        $this->post('/assets', [
            'item_id' => $item->id,
            'asset_tag' => 'MDO-001',
            'status' => 'in_stock',
        ])->assertRedirect();

        $this->assertDatabaseHas('assets', ['item_id' => $item->id, 'asset_tag' => 'MDO-001']);
    }

    public function test_install_moves_a_serialized_asset_to_its_destination(): void
    {
        $warehouse = $this->mainLocation();
        $office = Location::factory()->create(['name' => 'Alcaldía', 'code' => 'MDO-ALC']);
        $item = Item::factory()->create(['tracking_type' => 'serialized', 'type' => 'equipment']);
        $asset = Asset::factory()->create(['item_id' => $item->id, 'location_id' => $warehouse->id]);
        Stock::factory()->create(['item_id' => $item->id, 'location_id' => $warehouse->id, 'quantity' => 1]);

        $this->post('/movements', [
            'item_id' => $item->id,
            'asset_id' => $asset->id,
            'type' => 'install',
            'quantity' => 1,
            'from_location_id' => $warehouse->id,
            'to_location_id' => $office->id,
        ])->assertRedirect();

        $this->assertDatabaseHas('assets', [
            'id' => $asset->id,
            'location_id' => $office->id,
            'status' => 'assigned',
        ]);
        $this->assertDatabaseHas('stocks', [
            'item_id' => $item->id,
            'location_id' => $warehouse->id,
            'quantity' => 0,
        ]);
    }

    private function createItem(array $overrides = []): Item
    {
        return Item::factory()->create(array_merge($this->itemPayload(), $overrides));
    }

    private function mainLocation(): Location
    {
        return Location::factory()->create([
            'name' => 'Municipalidad de Doñihue',
            'code' => 'MDO-CENTRAL',
            'type' => 'site',
        ]);
    }

    private function itemPayload(): array
    {
        return [
            'sku' => 'TON-001',
            'name' => 'Tóner de prueba',
            'category' => 'Impresión',
            'type' => 'supply',
            'tracking_type' => 'bulk',
            'unit' => 'unidad',
            'default_min_stock' => 3,
            'active' => true,
        ];
    }
}
