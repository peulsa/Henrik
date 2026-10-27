<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveLocationRequest;
use App\Models\Location;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(): View
    {
        $locations = Location::query()->with(['parent', 'stocks.item'])->orderBy('name')->get();
        $mapLocations = $locations
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->map(fn (Location $location): array => [
                'name' => $location->name,
                'lat' => (float) $location->latitude,
                'lng' => (float) $location->longitude,
                'url' => route('locations.show', $location),
            ])->values();

        return view('locations.index', compact('locations', 'mapLocations'));
    }

    public function create(): View
    {
        return view('locations.create', ['parents' => $this->parents()]);
    }

    public function store(SaveLocationRequest $request): RedirectResponse
    {
        $location = Location::query()->create($request->validated());

        return redirect()->route('locations.show', $location)->with('success', 'Ubicación creada correctamente.');
    }

    public function show(Location $location): View
    {
        $location->load(['parent', 'children', 'stocks.item', 'assets.item']);

        return view('locations.show', compact('location'));
    }

    public function inventory(Location $location): View
    {
        $location->load(['items.itemType', 'items.serviceAreas', 'items.stocks', 'stocks.item.itemType', 'assets.item.itemType', 'assets.item.serviceAreas']);

        return view('locations.inventory', compact('location'));
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
