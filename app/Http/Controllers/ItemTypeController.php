<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveItemTypeRequest;
use App\Models\ItemType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ItemTypeController extends Controller
{
    public function index(): View
    {
        return view('item-types.index', [
            'types' => ItemType::query()->withCount('items')->orderBy('name')->get(),
        ]);
    }

    public function store(SaveItemTypeRequest $request): RedirectResponse
    {
        ItemType::query()->create($request->validated());

        return back()->with('success', 'Tipo de inventario creado correctamente.');
    }

    public function show(ItemType $itemType): RedirectResponse
    {
        return redirect()->route('item-types.edit', $itemType);
    }

    public function edit(ItemType $itemType): View
    {
        return view('item-types.edit', compact('itemType'));
    }

    public function update(SaveItemTypeRequest $request, ItemType $itemType): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $itemType): void {
            if ($data['slug'] !== $itemType->slug) {
                $itemType->items()->update(['type' => $data['slug']]);
            }

            $itemType->update($data);
        });

        return redirect()->route('item-types.index')->with('success', 'Tipo de inventario actualizado.');
    }

    public function destroy(ItemType $itemType): RedirectResponse
    {
        if ($itemType->items()->exists()) {
            return back()->withErrors(['type' => 'No puedes eliminar un tipo que todavía tiene artículos asociados.']);
        }

        $itemType->delete();

        return back()->with('success', 'Tipo de inventario eliminado.');
    }
}
