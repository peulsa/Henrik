<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveItemRequest;
use App\Models\Item;
use App\Models\ItemType;
use App\Models\Location;
use App\Models\ServiceArea;
use App\Models\Stock;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItemController extends Controller
{
    public function index(Request $request): View
    {
        $items = Item::query()
            ->with(['stocks', 'serviceAreas', 'itemType', 'location'])
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = '%'.$request->string('search')->trim().'%';
                $query->where(fn ($query) => $query
                    ->where('name', 'like', $search)
                    ->orWhere('sku', 'like', $search)
                    ->orWhere('brand', 'like', $search)
                    ->orWhere('model', 'like', $search));
            })
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->string('type')))
            ->orderBy('name')->orderBy('id')
            ->paginate(20)
            ->withQueryString();

        return view('items.index', [
            'items' => $items,
            'types' => ItemType::query()->where('active', true)->orderBy('name')->get(),
            'locations' => Location::query()->where('active', true)->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('items.create', [
            'areas' => ServiceArea::query()->where('active', true)->orderBy('name')->get(),
            'types' => ItemType::query()->where('active', true)->orderBy('name')->get(),
            'locations' => Location::query()->where('active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(SaveItemRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['unit'] = 'unidad';
        $data['tracking_type'] = 'serialized';
        $areaIds = $data['service_area_ids'] ?? [];
        unset($data['service_area_ids']);

        $item = Item::query()->create($data);
        $item->serviceAreas()->sync($areaIds);

        Stock::query()->firstOrCreate([
            'item_id' => $item->id,
            'location_id' => $item->location_id,
        ], ['quantity' => 0]);

        return redirect()->route('items.show', $item)->with('success', 'Insumo creado correctamente.');
    }

    public function show(Item $item): View
    {
        $item->load([
            'itemType',
            'location',
            'serviceAreas',
            'stocks.location',
            'assets.location',
            'movements' => fn ($query) => $query->with(['fromLocation', 'toLocation'])->latest()->take(20),
        ]);

        return view('items.show', compact('item'));
    }

    public function edit(Item $item): View
    {
        return view('items.edit', [
            'item' => $item->load('serviceAreas'),
            'areas' => ServiceArea::query()->where('active', true)->orderBy('name')->get(),
            'types' => ItemType::query()->where('active', true)->orderBy('name')->get(),
            'locations' => Location::query()->where('active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(SaveItemRequest $request, Item $item): RedirectResponse
    {
        $data = $request->validated();
        $data['unit'] = 'unidad';
        $data['tracking_type'] = 'serialized';
        $areaIds = $data['service_area_ids'] ?? [];
        unset($data['service_area_ids']);
        $item->update($data);
        $item->serviceAreas()->sync($areaIds);
        Stock::query()->firstOrCreate([
            'item_id' => $item->id,
            'location_id' => $item->location_id,
        ], ['quantity' => 0]);

        return redirect()->route('items.show', $item)->with('success', 'Insumo actualizado correctamente.');
    }

    public function destroy(Item $item): RedirectResponse
    {
        $item->delete();

        return redirect()->route('items.index')->with('success', 'Insumo dado de baja; su historial se conserva.');
    }
}
