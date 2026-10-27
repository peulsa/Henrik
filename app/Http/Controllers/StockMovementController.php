<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuickStoreItemRequest;
use App\Http\Requests\StoreStockMovementRequest;
use App\Http\Requests\UpdateReceiptRequest;
use App\Models\Item;
use App\Models\ItemType;
use App\Models\Location;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockMovementController extends Controller
{
    public function receive(Request $request): View
    {
        return view('stock.receive', [
            'items' => Item::query()->where('active', true)->orderBy('name')->get(),
            'locations' => Location::query()->where('active', true)->orderBy('name')->get(),
            'types' => ItemType::query()->where('active', true)->orderBy('name')->get(),
            'selectedItem' => $request->integer('item_id') ?: null,
        ]);
    }

    public function quickStoreItem(QuickStoreItemRequest $request): RedirectResponse
    {
        $item = Item::query()->create([
            ...$request->validated(),
            'unit' => 'unidad',
            'tracking_type' => 'serialized',
            'default_min_stock' => 0,
            'active' => true,
        ]);
        Stock::query()->create(['item_id' => $item->id, 'location_id' => $item->location_id, 'quantity' => 0]);

        return redirect()->route('stock.receive.create', ['item_id' => $item->id])
            ->with('success', 'Artículo creado. Ahora registra la cantidad recibida.');
    }

    public function index(Request $request): View
    {
        $movements = StockMovement::query()
            ->with(['item', 'asset', 'fromLocation', 'toLocation'])
            ->when($request->filled('item_id'), fn ($query) => $query->where('item_id', $request->integer('item_id')))
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->string('type')))
            ->latest()->latest('id')
            ->paginate(30)
            ->withQueryString();

        return view('movements.index', [
            'movements' => $movements,
            'items' => Item::query()->orderBy('name')->get(),
        ]);
    }

    public function create(Request $request): View
    {
        return view('movements.create', [
            'items' => Item::query()->where('active', true)->with('assets')->orderBy('name')->get(),
            'locations' => Location::query()->where('active', true)->orderBy('name')->get(),
            'selectedItem' => $request->integer('item_id') ?: null,
        ]);
    }

    public function store(StoreStockMovementRequest $request, InventoryService $inventory): RedirectResponse
    {
        $movement = $inventory->record($request->validated());

        return redirect()->route('movements.show', $movement)->with('success', 'Movimiento registrado correctamente.');
    }

    public function show(StockMovement $movement): View
    {
        return view('movements.show', ['movement' => $movement->load(['item', 'asset', 'fromLocation', 'toLocation'])]);
    }

    public function edit(StockMovement $movement): View
    {
        abort_unless($movement->type === 'receive', 404);

        return view('stock.edit-receipt', [
            'movement' => $movement,
            'items' => Item::query()->where('active', true)->orderBy('name')->get(),
            'locations' => Location::query()->where('active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateReceiptRequest $request, StockMovement $movement, InventoryService $inventory): RedirectResponse
    {
        abort_unless($movement->type === 'receive', 404);
        $inventory->updateReceipt($movement, $request->validated());

        return redirect()->route('movements.show', $movement)->with('success', 'Recepción actualizada correctamente.');
    }

    public function destroy(StockMovement $movement, InventoryService $inventory): RedirectResponse
    {
        abort_unless($movement->type === 'receive', 404);
        $inventory->deleteReceipt($movement);

        return redirect()->route('movements.index')->with('success', 'Recepción eliminada y stock corregido.');
    }
}
