<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStockMovementRequest;
use App\Models\Item;
use App\Models\Location;
use App\Models\StockMovement;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockMovementController extends Controller
{
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
}
