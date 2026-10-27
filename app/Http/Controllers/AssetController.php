<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveAssetRequest;
use App\Models\Asset;
use App\Models\Item;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AssetController extends Controller
{
    public function store(SaveAssetRequest $request): RedirectResponse
    {
        $asset = Asset::query()->create($request->validated());

        return redirect()->route('assets.show', $asset)->with('success', 'Equipo identificado correctamente.');
    }

    public function show(Asset $asset): View
    {
        $asset->load(['item', 'location', 'movements.fromLocation', 'movements.toLocation']);

        return view('assets.show', compact('asset'));
    }

    public function edit(Asset $asset): View
    {
        return view('assets.edit', [
            'asset' => $asset,
            'items' => Item::query()->where('tracking_type', 'serialized')->orderBy('name')->get(),
            'locations' => Location::query()->where('active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(SaveAssetRequest $request, Asset $asset): RedirectResponse
    {
        $asset->update($request->validated());

        return redirect()->route('assets.show', $asset)->with('success', 'Equipo actualizado correctamente.');
    }

    public function destroy(Asset $asset): RedirectResponse
    {
        $item = $asset->item;
        $asset->delete();

        return redirect()->route('items.show', $item)->with('success', 'Equipo dado de baja; su historial se conserva.');
    }
}
