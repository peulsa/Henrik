<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveLocationRequest;
use App\Models\Item;
use App\Models\Location;
use App\Models\Stock;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(): View
    {
        $locations = Location::query()->with(['parent', 'stocks.item'])->orderBy('name')->get();

        return view('locations.index', compact('locations'));
    }

    public function create(): View
    {
        return view('locations.create', ['parents' => $this->parents()]);
    }

    public function store(SaveLocationRequest $request): RedirectResponse
    {
        $location = Location::query()->create($request->validated());
        Item::query()->where('active', true)->each(fn (Item $item) => Stock::query()->firstOrCreate([
            'item_id' => $item->id,
            'location_id' => $location->id,
        ], ['quantity' => 0]));

        return redirect()->route('locations.show', $location)->with('success', 'Ubicación creada correctamente.');
    }

    public function show(Location $location): View
    {
        $location->load(['parent', 'children', 'stocks.item', 'assets.item']);

        return view('locations.show', compact('location'));
    }

    public function edit(Location $location): View
    {
        return view('locations.edit', ['location' => $location, 'parents' => $this->parents($location)]);
    }

    public function update(SaveLocationRequest $request, Location $location): RedirectResponse
    {
        $location->update($request->validated());

        return redirect()->route('locations.show', $location)->with('success', 'Ubicación actualizada correctamente.');
    }

    public function destroy(Location $location): RedirectResponse
    {
        if ($location->stocks()->where('quantity', '>', 0)->exists() || $location->assets()->exists()) {
            return back()->withErrors(['location' => 'Traslada el stock y los equipos antes de dar de baja esta ubicación.']);
        }

        $location->delete();

        return redirect()->route('locations.index')->with('success', 'Ubicación dada de baja; su historial se conserva.');
    }

    /** @return Collection<int, Location> */
    private function parents(?Location $excluded = null): Collection
    {
        return Location::query()
            ->where('active', true)
            ->when($excluded, fn ($query) => $query->whereKeyNot($excluded->id))
            ->orderBy('name')
            ->get();
    }
}
